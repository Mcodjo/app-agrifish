<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Payment;
use App\Services\Shop\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $payments)
    {
    }

    /** Retour du client depuis la page de paiement : le statut est relu chez le prestataire. */
    public function back(Payment $payment): RedirectResponse
    {
        $status = $this->payments->sync($payment);

        $flash = match ($status) {
            'approved' => 'payment-approved',
            'pending' => 'payment-pending',
            default => 'payment-failed',
        };

        return redirect($payment->order->publicUrl())->with('status', $flash);
    }

    public function simulate(Payment $payment): View
    {
        abort_unless($payment->provider === 'simulation' && ! app()->isProduction(), 404);

        return view('shop.payment-simulation', ['payment' => $payment->load('order')]);
    }

    public function confirmSimulation(Request $request, Payment $payment): RedirectResponse
    {
        abort_unless($payment->provider === 'simulation' && ! app()->isProduction() && $payment->status === 'pending', 404);

        $outcome = $request->input('outcome') === 'approved' ? 'approved' : 'declined';
        $payment->update(['payload' => array_merge($payment->payload ?? [], ['simulated' => $outcome])]);

        return redirect()->route('shop.payment.return', $payment);
    }

    public function webhook(Request $request, string $provider): Response
    {
        abort_unless($provider === 'fedapay', 404);

        $reference = $this->payments->gateway('fedapay')->referenceFromWebhook($request);

        if (! $reference) {
            return response('Signature invalide', 400);
        }

        Payment::query()->where('provider', 'fedapay')->where('reference', $reference)->get()
            ->each(fn (Payment $payment) => $this->payments->sync($payment));

        return response('ok');
    }
}
