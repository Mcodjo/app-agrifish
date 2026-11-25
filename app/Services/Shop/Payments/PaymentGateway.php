<?php

namespace App\Services\Shop\Payments;

use App\Models\Shop\Payment;
use Illuminate\Http\Request;

interface PaymentGateway
{
    public function name(): string;

    /** Crée la transaction chez le prestataire et renvoie l'adresse de la page de paiement. */
    public function checkout(Payment $payment): string;

    /** Statut de la transaction chez le prestataire : approved, pending, declined ou canceled. */
    public function status(Payment $payment): string;

    /** Référence de transaction d'une notification authentifiée, ou null si la signature est invalide. */
    public function referenceFromWebhook(Request $request): ?string;
}
