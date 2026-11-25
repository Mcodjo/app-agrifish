<?php

namespace App\Services\Shop;

use App\Models\Shop\Cart;
use App\Models\Shop\CartItem;
use App\Models\Shop\Coupon;
use App\Models\Shop\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CartService
{
    private ?Cart $cart = null;

    private bool $resolved = false;

    private ?int $requestId = null;

    public function current(bool $create = false): ?Cart
    {
        $request = request();

        // Le service peut survivre à la requête (contrôleur mis en cache, worker) : on repart de zéro à chaque requête.
        if ($this->requestId !== spl_object_id($request)) {
            $this->requestId = spl_object_id($request);
            $this->cart = null;
            $this->resolved = false;
        }

        if ($this->resolved && ($this->cart || ! $create)) {
            return $this->cart;
        }

        $user = $request->user();
        $token = $request->cookie(config('shop.cart_cookie'));
        $cart = null;

        if ($user) {
            $cart = Cart::query()->where('user_id', $user->id)->first();

            if (! $cart && $token) {
                $cart = Cart::query()->where('token', $token)->whereNull('user_id')->first();
                $cart?->update(['user_id' => $user->id]);
            }
        } elseif ($token) {
            $cart = Cart::query()->where('token', $token)->whereNull('user_id')->first();
        }

        if (! $cart && $create) {
            $cart = Cart::create(['token' => (string) Str::uuid(), 'user_id' => $user?->id]);
        }

        if ($cart && ! $user && $cart->token !== $token) {
            Cookie::queue(config('shop.cart_cookie'), $cart->token, config('shop.cart_lifetime_days') * 1440);
        }

        $cart?->touch();
        $this->cart = $cart;
        $this->resolved = true;

        return $cart;
    }

    /** Au moment de la connexion : le panier invité rejoint celui du compte. */
    public function mergeGuestCartInto(User $user): void
    {
        $token = request()->cookie(config('shop.cart_cookie'));
        $guest = $token ? Cart::query()->where('token', $token)->whereNull('user_id')->with('items')->first() : null;

        if (! $guest) {
            return;
        }

        $target = Cart::query()->where('user_id', $user->id)->first();

        if (! $target) {
            $guest->update(['user_id' => $user->id]);

            return;
        }

        foreach ($guest->items as $item) {
            $existing = $target->items()->where('product_variant_id', $item->product_variant_id)->first();
            $existing
                ? $existing->update(['quantity' => min(config('shop.max_quantity_per_line'), $existing->quantity + $item->quantity)])
                : $target->items()->create(['product_variant_id' => $item->product_variant_id, 'quantity' => $item->quantity]);
        }

        $guest->delete();
        $this->resolved = false;
    }

    public function add(ProductVariant $variant, int $quantity): void
    {
        $this->assertSellable($variant);

        $cart = $this->current(true);
        $item = $cart->items()->where('product_variant_id', $variant->id)->first();
        $wanted = ($item?->quantity ?? 0) + $quantity;

        if ($wanted > $variant->stock) {
            throw ValidationException::withMessages([
                'quantity' => $variant->stock > 0
                    ? 'Il reste '.$variant->stock.' exemplaire(s) disponible(s) pour ce produit.'
                    : 'Ce produit est en rupture de stock.',
            ]);
        }

        $item
            ? $item->update(['quantity' => $wanted])
            : $cart->items()->create(['product_variant_id' => $variant->id, 'quantity' => $quantity]);
    }

    public function update(CartItem $item, int $quantity): void
    {
        $this->assertOwns($item);

        if ($quantity < 1) {
            $item->delete();

            return;
        }

        $item->loadMissing('variant');
        $item->update(['quantity' => min($quantity, max(1, $item->variant->stock))]);
    }

    public function remove(CartItem $item): void
    {
        $this->assertOwns($item);
        $item->delete();
    }

    public function applyCoupon(string $code): void
    {
        $cart = $this->current(true);
        $coupon = Coupon::query()->where('code', mb_strtoupper(trim($code)))->first();

        if (! $coupon) {
            throw ValidationException::withMessages(['code' => 'Ce code promo n’existe pas.']);
        }

        if ($problem = $coupon->problemFor($this->summary()['subtotal'])) {
            throw ValidationException::withMessages(['code' => $problem]);
        }

        $cart->update(['coupon_id' => $coupon->id]);
    }

    public function removeCoupon(): void
    {
        $this->current()?->update(['coupon_id' => null]);
    }

    public function count(): int
    {
        return (int) ($this->current()?->items()->sum('quantity') ?? 0);
    }

    /**
     * Lignes du panier aux prix du moment, avec les problèmes éventuels (rupture, produit retiré).
     *
     * @return array{lines: array, subtotal: int, discount: int, total: int, count: int, coupon: ?Coupon, coupon_problem: ?string, problems: array}
     */
    public function summary(): array
    {
        $cart = $this->current();
        $lines = [];
        $problems = [];

        foreach ($cart?->items()->with('variant.product.images')->get() ?? [] as $item) {
            $variant = $item->variant;
            $product = $variant?->product;
            $sellable = $product && ! $product->trashed() && $product->is_published;
            $price = $variant ? $variant->currentPrice() : 0;

            if (! $sellable) {
                $problems[] = ($product?->name ?? 'Un produit').' n’est plus en vente.';
            } elseif ($variant->stock < $item->quantity) {
                $problems[] = $variant->displayName().' : '.($variant->stock > 0 ? 'il en reste '.$variant->stock.'.' : 'rupture de stock.');
            }

            $lines[] = [
                'item' => $item,
                'variant' => $variant,
                'product' => $product,
                'unit_price' => $price,
                'regular_price' => $variant?->price ?? 0,
                'quantity' => $item->quantity,
                'line_total' => $price * $item->quantity,
                'sellable' => $sellable && $variant->stock >= $item->quantity,
            ];
        }

        $subtotal = array_sum(array_column($lines, 'line_total'));
        $coupon = $cart?->coupon_id ? Coupon::query()->find($cart->coupon_id) : null;
        $couponProblem = $coupon?->problemFor($subtotal);
        $discount = $coupon && ! $couponProblem ? $coupon->discountFor($subtotal) : 0;

        return [
            'lines' => $lines,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $subtotal - $discount,
            'count' => array_sum(array_column($lines, 'quantity')),
            'coupon' => $coupon,
            'coupon_problem' => $couponProblem,
            'problems' => $problems,
        ];
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->update(['coupon_id' => null]);
    }

    private function assertSellable(ProductVariant $variant): void
    {
        $product = $variant->product;

        if (! $product || $product->trashed() || ! $product->is_published) {
            throw ValidationException::withMessages(['quantity' => 'Ce produit n’est pas disponible.']);
        }
    }

    private function assertOwns(CartItem $item): void
    {
        abort_unless($this->current() && $item->cart_id === $this->current()->id, 404);
    }
}
