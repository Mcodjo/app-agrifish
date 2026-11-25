<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop\Order;
use App\Services\Shop\OrderService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders)
    {
    }

    public function index(Request $request): View
    {
        $status = array_key_exists((string) $request->query('statut'), Order::STATUSES) || $request->query('statut') === 'a-traiter' ? $request->query('statut') : null;
        $method = array_key_exists((string) $request->query('paiement'), Order::PAYMENT_METHODS) ? $request->query('paiement') : null;
        $search = trim((string) $request->query('q', ''));
        $from = $request->date('du');
        $to = $request->date('au');

        $orders = Order::query()
            ->withCount('items')
            ->when($status === 'a-traiter', fn (Builder $q) => $q->whereIn('status', ['new', 'paid', 'preparing', 'ready']))
            ->when($status && $status !== 'a-traiter', fn (Builder $q) => $q->where('status', $status))
            ->when($method, fn (Builder $q) => $q->where('payment_method', $method))
            ->when($from, fn (Builder $q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn (Builder $q) => $q->whereDate('created_at', '<=', $to))
            ->when($search !== '', fn (Builder $q) => $q->where(function (Builder $q) use ($search) {
                $q->where('number', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('first_name', 'like', "%{$search}%");
            }))
            ->orderByRaw("case when status = 'paid' then 0 when status = 'new' then 1 else 2 end")
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('shop-admin.orders.index', [
            'orders' => $orders,
            'counts' => Order::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'status' => $status,
            'method' => $method,
            'search' => $search,
            'from' => $from?->format('Y-m-d'),
            'to' => $to?->format('Y-m-d'),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items.product.images', 'items.variant', 'payments', 'refunds.user', 'events.user', 'user']);

        return view('shop-admin.orders.show', ['order' => $order]);
    }

    public function advance(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->nextStatus(), 422);

        if ($order->nextStatus() === 'completed' && $order->payment_status === 'to_collect' && $request->boolean('collected')) {
            $this->orders->collectCash($order, $request->user());
        }

        $this->orders->advance($order, $request->user());

        return back()->with('status', 'order-updated');
    }

    public function collect(Request $request, Order $order): RedirectResponse
    {
        $this->orders->collectCash($order, $request->user());

        return back()->with('status', 'order-collected');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->isOpen(), 422);
        $validated = $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        $this->orders->cancel($order, $request->user(), $validated['reason'] ?? null);

        return back()->with('status', 'order-cancelled');
    }

    public function refund(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:500'],
            'restock' => ['nullable', 'boolean'],
        ]);

        $this->orders->refund($order, (int) $validated['amount'], $validated['reason'] ?? null, $request->boolean('restock'), $request->user());

        return back()->with('status', 'order-refunded');
    }

    public function note(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate(['note' => ['required', 'string', 'max:500']]);
        $order->record('note', $request->user(), ['details' => $validated['note']]);

        return back()->with('status', 'note-added');
    }
}
