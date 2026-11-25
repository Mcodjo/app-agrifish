<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Shop\Order;
use App\Services\Shop\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filter = in_array($request->query('filtre'), ['en-cours', 'terminees', 'annulees'], true) ? $request->query('filtre') : null;

        $orders = $request->user()->orders()
            ->withCount('items')
            ->when($filter === 'en-cours', fn ($q) => $q->whereIn('status', Order::OPEN))
            ->when($filter === 'terminees', fn ($q) => $q->where('status', 'completed'))
            ->when($filter === 'annulees', fn ($q) => $q->whereIn('status', ['cancelled', 'refunded']))
            ->paginate(10)
            ->withQueryString();

        return view('account.orders.index', ['orders' => $orders, 'filter' => $filter]);
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $order->load(['items.product.images', 'events', 'payments']);
        $reviewed = $request->user()->reviews()->pluck('product_id')->all();

        return view('account.orders.show', ['order' => $order, 'reviewed' => $reviewed]);
    }

    /** Remet dans le panier les articles d'une commande passée, dans la limite du stock. */
    public function reorder(Request $request, Order $order, CartService $cart): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $order->load('items.variant.product');
        $added = 0;
        $missing = [];

        foreach ($order->items as $item) {
            $variant = $item->variant;

            if (! $variant || ! $variant->product || $variant->product->trashed() || ! $variant->product->is_published || $variant->stock < 1) {
                $missing[] = $item->label();
                continue;
            }

            try {
                $cart->add($variant, min($item->quantity, $variant->stock));
                $added++;
            } catch (ValidationException) {
                $missing[] = $item->label();
            }
        }

        $redirect = redirect()->route('shop.cart');

        if ($missing) {
            $redirect->with('reorder_missing', $missing);
        }

        return $redirect->with('status', $added ? 'reorder-done' : 'reorder-empty');
    }
}
