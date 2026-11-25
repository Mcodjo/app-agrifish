<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\CartItem;
use App\Models\Shop\ProductVariant;
use App\Services\Shop\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private CartService $cart)
    {
    }

    public function show(): View
    {
        return view('shop.cart', ['summary' => $this->cart->summary()]);
    }

    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.config('shop.max_quantity_per_line')],
        ]);

        $variant = ProductVariant::query()->with('product')->findOrFail($validated['variant_id']);
        $this->cart->add($variant, (int) $validated['quantity']);

        return $request->boolean('buy_now')
            ? redirect()->route('shop.checkout')
            : back()->with('status', 'cart-added');
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:'.config('shop.max_quantity_per_line')]]);
        $this->cart->update($item, (int) $validated['quantity']);

        return redirect()->route('shop.cart');
    }

    public function remove(CartItem $item): RedirectResponse
    {
        $this->cart->remove($item);

        return redirect()->route('shop.cart')->with('status', 'cart-removed');
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $validated = $request->validate(['code' => ['required', 'string', 'max:40']]);
        $this->cart->applyCoupon($validated['code']);

        return redirect()->route('shop.cart')->with('status', 'coupon-applied');
    }

    public function removeCoupon(): RedirectResponse
    {
        $this->cart->removeCoupon();

        return redirect()->route('shop.cart');
    }
}
