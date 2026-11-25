<?php

namespace App\Services\Shop;

use App\Models\Setting;
use App\Models\Shop\Order;
use App\Models\Shop\Payment;
use App\Services\Shop\Payments\FedaPayGateway;
use App\Services\Shop\Payments\PaymentGateway;
use App\Services\Shop\Payments\SimulationGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentService
{
    public function __construct(private OrderService $orders)
    {
    }

    public function gateway(?string $name = null): PaymentGateway
    {
        return match ($name ?? config('shop.payment_driver')) {
            'fedapay' => app(FedaPayGateway::class),
            default => app(SimulationGateway::class),
        };
    }

    /** Le paiement en ligne est proposé seulement s'il est activé et réellement configuré. */
    public function onlineAvailable(): bool
    {
        if (! Setting::enabled('online_payment')) {
            return false;
        }

        return match (config('shop.payment_driver')) {
            'fedapay' => (bool) config('services.fedapay.secret_key'),
            'simulation' => ! app()->isProduction(),
            default => false,
        };
    }

    public function cashAvailable(): bool
    {
        return Setting::enabled('cash_on_delivery');
    }

    /** Ouvre une tentative de paiement et renvoie l'adresse de la page de paiement, ou null en cas d'échec. */
    public function start(Order $order): ?string
    {
        abort_unless($order->canRetryPayment(), 409);

        $order->payments()->where('status', 'pending')->update(['status' => 'canceled']);

        $gateway = $this->gateway();
        $payment = $order->payments()->create([
            'provider' => $gateway->name(),
            'amount' => $order->total,
            'status' => 'pending',
        ]);

        try {
            return $gateway->checkout($payment);
        } catch (Throwable $e) {
            Log::error('Échec de création du paiement', ['order' => $order->number, 'error' => $e->getMessage()]);
            $payment->update(['status' => 'canceled']);

            return null;
        }
    }

    /** Relit le statut chez le prestataire et met la commande à jour. Renvoie le statut du paiement. */
    public function sync(Payment $payment): string
    {
        if ($payment->status !== 'pending') {
            return $payment->status;
        }

        try {
            $status = $this->gateway($payment->provider)->status($payment);
        } catch (Throwable $e) {
            Log::warning('Statut de paiement indisponible', ['payment' => $payment->id, 'error' => $e->getMessage()]);

            return 'pending';
        }

        if ($status === 'pending') {
            return 'pending';
        }

        $changed = DB::transaction(function () use ($payment, $status) {
            $locked = Payment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($locked->status !== 'pending') {
                return false;
            }

            $locked->update(['status' => $status]);
            $payment->setRawAttributes($locked->getAttributes(), true);

            return true;
        });

        if ($changed) {
            $order = $payment->order;

            $status === 'approved'
                ? $this->orders->markPaid($order, $payment)
                : $order->record('payment_failed', null, ['details' => $payment->providerLabel()]);
        }

        return $payment->status;
    }
}
