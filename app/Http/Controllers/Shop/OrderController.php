<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Order;
use App\Services\Shop\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function show(Request $request, Order $order): View|RedirectResponse
    {
        $this->authorizeAccess($request, $order);

        // Le client connecté retrouve sa commande dans son espace.
        if ($request->user() && $order->user_id === $request->user()->id) {
            return redirect()->route('account.orders.show', $order);
        }

        $order->load(['items.product.images', 'events', 'payments']);

        return view('shop.order', ['order' => $order]);
    }

    public function pay(Request $request, Order $order, PaymentService $payments): RedirectResponse
    {
        $this->authorizeAccess($request, $order);

        if (! $order->canRetryPayment() || ! $payments->onlineAvailable()) {
            return back()->with('status', 'payment-unavailable');
        }

        $url = $payments->start($order);

        return $url ? redirect()->away($url) : back()->with('status', 'payment-unavailable');
    }

    public function invoice(Request $request, Order $order): Response
    {
        $this->authorizeAccess($request, $order);
        abort_unless($order->hasInvoice(), 404);

        $order->load('items');

        return Pdf::loadView('shop.invoice', ['order' => $order, 'legal' => config('site.legal'), 'contact' => config('site.contact')])
            ->setPaper('a4')
            ->download('facture-'.$order->number.'.pdf');
    }

    /** Accès : le client propriétaire, l'équipe boutique, ou le lien personnel reçu par e-mail. */
    public static function canAccess(Request $request, Order $order): bool
    {
        $user = $request->user();

        if ($user && ($user->canManageShop() || $order->user_id === $user->id)) {
            return true;
        }

        $key = (string) $request->query('cle', '');

        if ($key !== '' && hash_equals($order->access_token, $key)) {
            session()->put('orders_access.'.$order->number, true);

            return true;
        }

        return (bool) session('orders_access.'.$order->number);
    }

    private function authorizeAccess(Request $request, Order $order): void
    {
        abort_unless(self::canAccess($request, $order), 404);
    }
}
