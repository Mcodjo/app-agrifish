<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('shop-admin.categories', [
            'categories' => ProductCategory::query()->whereNull('parent_id')
                ->with(['children' => fn ($q) => $q->withCount('products')])->withCount('products')
                ->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        ProductCategory::create($data + ['slug' => $this->uniqueSlug($data['name'])]);

        return back()->with('status', 'category-saved');
    }

    public function update(Request $request, ProductCategory $category): RedirectResponse
    {
        $data = $this->validated($request, $category);
        $category->update($data);

        return back()->with('status', 'category-saved');
    }

    public function destroy(ProductCategory $category): RedirectResponse
    {
        if ($category->products()->exists() || $category->children()->exists()) {
            return back()->withErrors(['category' => 'La catégorie « '.$category->name.' » contient des produits ou des sous-catégories : déplacez-les d’abord.']);
        }

        $category->delete();

        return back()->with('status', 'category-deleted');
    }

    private function validated(Request $request, ?ProductCategory $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'parent_id' => ['nullable', 'integer', Rule::exists('product_categories', 'id')->whereNull('parent_id')],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ]);

        if ($category && ! empty($data['parent_id']) && ((int) $data['parent_id'] === $category->id || $category->children()->exists())) {
            throw ValidationException::withMessages(['parent_id' => 'Les catégories s’organisent sur deux niveaux au plus.']);
        }

        return ['name' => $data['name'], 'parent_id' => $data['parent_id'] ?? null, 'description' => $data['description'] ?? null, 'sort_order' => $data['sort_order'] ?? 0];
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'categorie';
        $slug = $base;
        $i = 2;

        while (ProductCategory::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
