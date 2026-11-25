<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Product;
use App\Models\Shop\ProductCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    private const SORTS = [
        'nouveautes' => 'Nouveautés',
        'prix-croissant' => 'Prix croissant',
        'prix-decroissant' => 'Prix décroissant',
        'nom' => 'Nom',
    ];

    public function index(Request $request): View
    {
        $categories = ProductCategory::query()->whereNull('parent_id')->with('children')->orderBy('sort_order')->orderBy('name')->get();
        $category = $request->filled('categorie') ? ProductCategory::query()->where('slug', $request->query('categorie'))->first() : null;
        $sort = array_key_exists((string) $request->query('tri'), self::SORTS) ? $request->query('tri') : 'nouveautes';
        $search = trim((string) $request->query('q', ''));
        $min = is_numeric($request->query('min')) ? (int) $request->query('min') : null;
        $max = is_numeric($request->query('max')) ? (int) $request->query('max') : null;
        $available = $request->boolean('disponible');

        // Prix de référence d'un produit : son plus petit prix de déclinaison.
        $minPrice = '(select min(price) from product_variants where product_variants.product_id = products.id)';

        $products = Product::query()
            ->published()
            ->with(['images', 'variants.product', 'category'])
            ->when($category, fn (Builder $q) => $q->whereIn('category_id', $category->selfAndChildIds()))
            ->when($search !== '', fn (Builder $q) => $q->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhereHas('variants', fn (Builder $v) => $v->where('sku', 'like', "%{$search}%"));
            }))
            ->when($min !== null, fn (Builder $q) => $q->whereRaw("$minPrice >= ?", [$min]))
            ->when($max !== null, fn (Builder $q) => $q->whereRaw("$minPrice <= ?", [$max]))
            ->when($available, fn (Builder $q) => $q->whereHas('variants', fn (Builder $v) => $v->where('stock', '>', 0)))
            ->when($sort === 'nouveautes', fn (Builder $q) => $q->latest())
            ->when($sort === 'nom', fn (Builder $q) => $q->orderBy('name'))
            ->when($sort === 'prix-croissant', fn (Builder $q) => $q->orderByRaw("$minPrice asc"))
            ->when($sort === 'prix-decroissant', fn (Builder $q) => $q->orderByRaw("$minPrice desc"))
            ->paginate(12)
            ->withQueryString();

        return view('shop.index', [
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $category,
            'sorts' => self::SORTS,
            'sort' => $sort,
            'search' => $search,
            'min' => $min,
            'max' => $max,
            'available' => $available,
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_published, 404);

        $product->load(['images', 'variants.product', 'category.parent', 'approvedReviews.user']);

        $related = Product::query()->published()
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn (Builder $q) => $q->where('category_id', $product->category_id))
            ->with(['images', 'variants.product'])
            ->latest()->take(4)->get();

        return view('shop.product', [
            'product' => $product,
            'related' => $related,
            'rating' => $product->averageRating(),
        ]);
    }
}
