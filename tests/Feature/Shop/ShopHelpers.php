<?php

namespace Tests\Feature\Shop;

use App\Models\Shop\Order;
use App\Models\Shop\Product;
use App\Models\Shop\ProductCategory;
use App\Models\Shop\ProductVariant;
use App\Models\User;
use Illuminate\Support\Str;

trait ShopHelpers
{
    protected function userWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    protected function makeProduct(array $attributes = [], array $variants = [['price' => 10000, 'stock' => 5]]): Product
    {
        $name = $attributes['name'] ?? 'Aliment poisson '.Str::random(4);

        $product = Product::create(array_merge([
            'name' => $name,
            'slug' => Str::slug($name),
            'reference' => 'REF-'.Str::upper(Str::random(6)),
            'is_published' => true,
            'low_stock_threshold' => 2,
        ], $attributes));

        foreach ($variants as $i => $variant) {
            $product->variants()->create(array_merge([
                'sku' => 'SKU-'.Str::upper(Str::random(8)),
                'name' => count($variants) > 1 ? 'Format '.($i + 1) : null,
                'sort_order' => $i,
            ], $variant));
        }

        return $product->load('variants');
    }

    protected function makeCategory(string $name, ?ProductCategory $parent = null): ProductCategory
    {
        return ProductCategory::create(['name' => $name, 'slug' => Str::slug($name), 'parent_id' => $parent?->id]);
    }

    /** Ajoute au panier et garde le cookie du panier pour les requêtes suivantes, comme un navigateur. */
    protected function addToCart(ProductVariant $variant, int $quantity = 1)
    {
        $response = $this->post(route('shop.cart.add'), ['variant_id' => $variant->id, 'quantity' => $quantity]);

        if ($cookie = $response->getCookie(config('shop.cart_cookie'))) {
            $this->withCookie($cookie->getName(), $cookie->getValue());
        }

        return $response;
    }

    protected function checkout(array $overrides = [])
    {
        return $this->post(route('shop.checkout.store'), array_merge([
            'first_name' => 'Awa',
            'last_name' => 'Koné',
            'email' => 'awa@example.com',
            'phone' => '+229 01 97 00 00 00',
            'city' => 'Cotonou',
            'district' => 'Akpakpa',
            'payment_method' => 'on_delivery',
            'cgv' => '1',
        ], $overrides));
    }

    protected function latestOrder(): Order
    {
        return Order::query()->latest('id')->firstOrFail();
    }
}
