<?php

namespace App\Services\Shop\Payments;

use App\Models\Shop\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/** Paiement de démonstration : une page interne permet d'accepter ou de refuser le paiement. Jamais en production. */
class SimulationGateway implements PaymentGateway
{
    public function name(): string
    {
        return 'simulation';
    }

    public function checkout(Payment $payment): string
    {
        $url = URL::temporarySignedRoute('shop.payment.simulate', now()->addHours(2), $payment);
        $payment->update(['reference' => 'TEST-'.Str::upper(Str::random(10)), 'checkout_url' => $url]);

        return $url;
    }

    public function status(Payment $payment): string
    {
        return $payment->payload['simulated'] ?? 'pending';
    }

    public function referenceFromWebhook(Request $request): ?string
    {
        return null;
    }
}
