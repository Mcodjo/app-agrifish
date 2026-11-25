<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop\Order;
use App\Models\Shop\OrderItem;
use App\Models\Shop\ProductReview;
use App\Models\Shop\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $paid = fn () => Order::query()->whereIn('payment_status', ['paid', 'partially_refunded', 'refunded']);
        $revenue = fn ($query) => (int) $query->sum(DB::raw('total - refunded_amount'));

        $monthOrders = $paid()->where('paid_at', '>=', now()->startOfMonth())->count();
        $monthRevenue = $revenue($paid()->where('paid_at', '>=', now()->startOfMonth()));

        $days = collect(range(13, 0))->map(fn ($i) => now()->subDays($i)->startOfDay());
        $daily = $paid()->where('paid_at', '>=', $days->first())->get(['paid_at', 'total', 'refunded_amount'])
            ->groupBy(fn ($o) => $o->paid_at->format('Y-m-d'))
            ->map(fn ($group) => $group->sum(fn ($o) => $o->total - $o->refunded_amount));

        $topProducts = OrderItem::query()
            ->select('product_name', DB::raw('sum(quantity) as quantity'), DB::raw('sum(line_total) as revenue'))
            ->whereHas('order', fn ($q) => $q->whereIn('payment_status', ['paid', 'partially_refunded'])->where('paid_at', '>=', now()->startOfMonth()))
            ->groupBy('product_name')
            ->orderByDesc('quantity')
            ->limit(10)
            ->get();

        return view('shop-admin.dashboard', [
            'kpis' => [
                'today' => $revenue($paid()->whereDate('paid_at', today())),
                'month' => $monthRevenue,
                'orders' => $monthOrders,
                'basket' => $monthOrders ? intdiv($monthRevenue, $monthOrders) : 0,
            ],
            'daily' => $days->map(fn ($day) => ['date' => $day, 'amount' => (int) ($daily[$day->format('Y-m-d')] ?? 0)]),
            'topProducts' => $topProducts,
            'toProcess' => Order::query()->whereIn('status', ['new', 'paid', 'preparing', 'ready'])
                ->orderByRaw("case status when 'paid' then 0 when 'new' then 1 when 'preparing' then 2 else 3 end")
                ->oldest()->take(8)->get(),
            'awaitingPayment' => Order::query()->where('status', 'pending_payment')->count(),
            'lowStock' => ProductVariant::query()->with('product')
                ->whereHas('product', fn ($q) => $q->whereColumn('product_variants.stock', '<=', 'products.low_stock_threshold'))
                ->orderBy('stock')->take(8)->get(),
            'pendingReviews' => ProductReview::query()->where('status', 'pending')->count(),
        ]);
    }
}
