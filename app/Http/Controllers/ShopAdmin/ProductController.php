<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop\OrderItem;
use App\Models\Shop\Product;
use App\Models\Shop\ProductCategory;
use App\Models\Shop\ProductVariant;
use App\Services\Shop\ProductImages;
use App\Services\Shop\StockService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private StockService $stock, private ProductImages $images)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $category = $request->integer('categorie') ?: null;
        $state = in_array($request->query('etat'), ['publies', 'brouillons', 'stock-bas', 'rupture'], true) ? $request->query('etat') : null;

        $products = Product::query()
            ->with(['images', 'variants.product', 'category'])
            ->when($search !== '', fn (Builder $q) => $q->where(fn (Builder $q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('reference', 'like', "%{$search}%")
                ->orWhereHas('variants', fn (Builder $v) => $v->where('sku', 'like', "%{$search}%"))))
            ->when($category, fn (Builder $q) => $q->whereIn('category_id', ProductCategory::query()->find($category)?->selfAndChildIds() ?? [$category]))
            ->when($state === 'publies', fn (Builder $q) => $q->where('is_published', true))
            ->when($state === 'brouillons', fn (Builder $q) => $q->where('is_published', false))
            ->when($state === 'stock-bas', fn (Builder $q) => $q->whereHas('variants', fn (Builder $v) => $v->whereColumn('product_variants.stock', '<=', 'products.low_stock_threshold')))
            ->when($state === 'rupture', fn (Builder $q) => $q->whereDoesntHave('variants', fn (Builder $v) => $v->where('stock', '>', 0)))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('shop-admin.products.index', [
            'products' => $products,
            'categories' => $this->categoryOptions(),
            'search' => $search,
            'category' => $category,
            'state' => $state,
        ]);
    }

    public function create(): View
    {
        return view('shop-admin.products.form', [
            'product' => new Product(['low_stock_threshold' => 5, 'is_published' => false]),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $product = DB::transaction(function () use ($request, $data) {
            $product = Product::create($data['product'] + ['slug' => $this->uniqueSlug($data['product']['name'])]);
            $this->syncVariants($product, $data['variants'], $request);
            $this->syncFiles($product, $request);

            return $product;
        });

        return redirect()->route('shop-admin.products.edit', $product)->with('status', 'product-created');
    }

    public function edit(Product $product): View
    {
        $product->load(['images', 'variants.movements.user', 'variants.movements.order', 'category']);

        return view('shop-admin.products.form', [
            'product' => $product,
            'categories' => $this->categoryOptions(),
            'movements' => $product->variants->flatMap->movements->sortByDesc('created_at')->take(30),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product);

        DB::transaction(function () use ($request, $product, $data) {
            $product->update($data['product']);
            $this->syncVariants($product, $data['variants'], $request);
            $this->syncFiles($product, $request);
        });

        return back()->with('status', 'product-updated');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Un produit déjà commandé est archivé (masqué) pour garder l'historique des ventes.
        $product->update(['is_published' => false]);
        $product->delete();

        return redirect()->route('shop-admin.products.index')->with('status', 'product-deleted');
    }

    public function adjustStock(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'variant_id' => ['required', Rule::exists('product_variants', 'id')->where('product_id', $product->id)],
            'type' => ['required', Rule::in(['restock', 'adjustment'])],
            'quantity' => ['required', 'integer', 'not_in:0', 'between:-100000,100000'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $variant = ProductVariant::query()->findOrFail($validated['variant_id']);

        if ($variant->stock + (int) $validated['quantity'] < 0) {
            throw ValidationException::withMessages(['quantity' => 'Le stock ne peut pas devenir négatif (stock actuel : '.$variant->stock.').']);
        }

        $this->stock->move($variant, (int) $validated['quantity'], $validated['type'], null, $request->user(), $validated['note'] ?? null);
        $this->stock->checkLowStock($variant);

        return back()->with('status', 'stock-updated');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $variantIds = $product?->variants()->pluck('id')->all() ?? [];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'reference' => ['required', 'string', 'max:60', Rule::unique('products', 'reference')->ignore($product?->id)],
            'category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'summary' => ['nullable', 'string', 'max:300'],
            'description' => ['nullable', 'string', 'max:10000'],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_published' => ['nullable', 'boolean'],
            'sale_starts_at' => ['nullable', 'date'],
            'sale_ends_at' => ['nullable', 'date', 'after:sale_starts_at'],
            'seo_title' => ['nullable', 'string', 'max:160'],
            'seo_description' => ['nullable', 'string', 'max:300'],
            'variants' => ['required', 'array', 'min:1', 'max:30'],
            'variants.*.id' => ['nullable', 'integer', Rule::in($variantIds)],
            'variants.*.name' => ['nullable', 'string', 'max:120'],
            'variants.*.sku' => ['required', 'string', 'max:60', 'distinct'],
            'variants.*.price' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'variants.*.sale_price' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'variants.*.remove' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array', 'max:'.config('shop.max_images')],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:'.config('shop.image_max_kb')],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer'],
            'cover_image' => ['nullable', 'integer'],
            'technical_sheet' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'remove_technical_sheet' => ['nullable', 'boolean'],
        ], [
            'variants.*.sku.distinct' => 'Deux déclinaisons ont le même SKU.',
            'sale_ends_at.after' => 'La fin de la promotion doit suivre son début.',
        ]);

        foreach ($validated['variants'] as $i => $variant) {
            $exists = ProductVariant::query()->where('sku', $variant['sku'])
                ->when($variant['id'] ?? null, fn ($q, $id) => $q->where('id', '!=', $id))->exists();

            if ($exists) {
                throw ValidationException::withMessages(["variants.$i.sku" => 'Ce SKU est déjà utilisé par un autre produit.']);
            }

            if (($variant['sale_price'] ?? null) !== null && $variant['sale_price'] >= $variant['price']) {
                throw ValidationException::withMessages(["variants.$i.sale_price" => 'Le prix promotionnel doit être inférieur au prix normal.']);
            }
        }

        $kept = collect($validated['variants'])->reject(fn ($v) => ! empty($v['remove']));

        if ($kept->isEmpty()) {
            throw ValidationException::withMessages(['variants' => 'Un produit garde au moins une déclinaison.']);
        }

        $imageCount = ($product?->images()->whereNotIn('id', $validated['remove_images'] ?? [])->count() ?? 0) + count($request->file('images', []));

        if ($request->boolean('is_published') && $imageCount === 0) {
            throw ValidationException::withMessages(['images' => 'Ajoutez au moins une photo avant de publier le produit.']);
        }

        if ($imageCount > config('shop.max_images')) {
            throw ValidationException::withMessages(['images' => 'Un produit compte au maximum '.config('shop.max_images').' photos.']);
        }

        return [
            'product' => [
                'name' => $validated['name'],
                'reference' => $validated['reference'],
                'category_id' => $validated['category_id'] ?? null,
                'summary' => $validated['summary'] ?? null,
                'description' => $validated['description'] ?? null,
                'low_stock_threshold' => $validated['low_stock_threshold'],
                'is_published' => $request->boolean('is_published'),
                'sale_starts_at' => $validated['sale_starts_at'] ?? null,
                'sale_ends_at' => $validated['sale_ends_at'] ?? null,
                'seo_title' => $validated['seo_title'] ?? null,
                'seo_description' => $validated['seo_description'] ?? null,
            ],
            'variants' => $validated['variants'],
        ];
    }

    private function syncVariants(Product $product, array $variants, Request $request): void
    {
        foreach (array_values($variants) as $position => $data) {
            $variant = ! empty($data['id']) ? $product->variants()->find($data['id']) : null;

            if ($variant && ! empty($data['remove'])) {
                if (OrderItem::query()->where('product_variant_id', $variant->id)->exists()) {
                    throw ValidationException::withMessages(['variants' => 'La déclinaison « '.$variant->label().' » a déjà été commandée : passez son stock à zéro plutôt que de la supprimer.']);
                }
                $variant->delete();
                continue;
            }

            if (! empty($data['remove'])) {
                continue;
            }

            $attributes = [
                'name' => ($data['name'] ?? null) ?: null,
                'sku' => $data['sku'],
                'price' => (int) $data['price'],
                'sale_price' => isset($data['sale_price']) ? (int) $data['sale_price'] : null,
                'sort_order' => $position,
            ];

            if ($variant) {
                $variant->update($attributes);
                continue;
            }

            $variant = $product->variants()->create($attributes + ['stock' => 0]);

            if (($initial = (int) ($data['stock'] ?? 0)) > 0) {
                $this->stock->move($variant, $initial, 'restock', null, $request->user(), 'Stock initial');
            }
        }
    }

    private function syncFiles(Product $product, Request $request): void
    {
        foreach ($product->images()->whereIn('id', $request->input('remove_images', []))->get() as $image) {
            $this->images->delete($image->path);
            $image->delete();
        }

        $next = (int) $product->images()->max('sort_order') + 1;

        foreach ($request->file('images', []) as $file) {
            $product->images()->create(['path' => $this->images->store($file), 'sort_order' => $next++]);
        }

        if ($cover = $request->integer('cover_image')) {
            $image = $product->images()->find($cover);

            if ($image) {
                $product->images()->where('id', '!=', $image->id)->increment('sort_order');
                $image->update(['sort_order' => 0]);
            }
        }

        if ($request->boolean('remove_technical_sheet') && $product->technical_sheet_path) {
            Storage::disk('public')->delete($product->technical_sheet_path);
            $product->update(['technical_sheet_path' => null]);
        }

        if ($request->hasFile('technical_sheet')) {
            $product->technical_sheet_path && Storage::disk('public')->delete($product->technical_sheet_path);
            $product->update(['technical_sheet_path' => $request->file('technical_sheet')->store('fiches-techniques', 'public')]);
        }
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'produit';
        $slug = $base;
        $i = 2;

        while (Product::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function categoryOptions()
    {
        return ProductCategory::query()->whereNull('parent_id')->with('children')->orderBy('sort_order')->orderBy('name')->get();
    }
}
