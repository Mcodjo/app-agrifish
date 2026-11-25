<?php

use App\Models\Shop\Order;
use App\Models\Shop\Payment;
use App\Services\Shop\OrderService;
use App\Services\Shop\PaymentService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('shop:sync-payments', function (PaymentService $payments) {
    $pending = Payment::query()
        ->where('status', 'pending')
        ->where('provider', '!=', 'cash')
        ->where('created_at', '<=', now()->subMinutes(2))
        ->where('created_at', '>=', now()->subDays(3))
        ->get();

    $pending->each(fn (Payment $payment) => $payments->sync($payment));
    $this->info($pending->count().' paiement(s) vérifié(s).');
})->purpose('Relit auprès du prestataire le statut des paiements en attente');

Artisan::command('shop:expire-orders', function (PaymentService $payments, OrderService $orders) {
    $expired = Order::query()
        ->where('status', 'pending_payment')
        ->whereNotNull('expires_at')
        ->where('expires_at', '<=', now())
        ->get();

    foreach ($expired as $order) {
        // Dernière vérification : un paiement peut être arrivé sans notification.
        $order->payments()->where('status', 'pending')->get()->each(fn (Payment $payment) => $payments->sync($payment));

        if ($order->refresh()->status === 'pending_payment') {
            $orders->cancel($order, null, 'paiement non reçu dans le délai', 'expired');
        }
    }

    $this->info($expired->count().' commande(s) traitée(s).');
})->purpose('Annule les commandes non payées dans le délai et libère leur stock');

Schedule::command('shop:sync-payments')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('shop:expire-orders')->everyTenMinutes()->withoutOverlapping();
