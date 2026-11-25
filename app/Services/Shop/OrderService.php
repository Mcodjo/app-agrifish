<?php

namespace App\Services\Shop;

use App\Models\Setting;
use App\Models\Shop\Cart;
use App\Models\Shop\Coupon;
use App\Models\Shop\Order;
use App\Models\Shop\OrderRefund;
use App\Models\Shop\Payment;
use App\Models\Shop\ProductVariant;
use App\Models\User;
use App\Notifications\Shop\NewShopOrder;
use App\Notifications\Shop\OrderPaid;
use App\Notifications\Shop\OrderPlaced;
use App\Notifications\Shop\OrderStatusUpdated;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as Notifier;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(private StockService $stock, private CartService $carts)
    {
    }

    /**
     * Transforme le panier en commande. Le stock est réservé immédiatement, sous verrou :
     * deux clients ne peuvent pas acheter le même dernier article.
     */
    public function place(Cart $cart, array $customer, string $paymentMethod, ?User $user): Order
    {
        $summary = $this->carts->summary();

        if ($summary['count'] === 0) {
            throw ValidationException::withMessages(['cart' => 'Votre panier est vide.']);
        }

        $order = DB::transaction(function () use ($cart, $summary, $customer, $paymentMethod, $user) {
            $variants = ProductVariant::query()
                ->whereIn('id', collect($summary['lines'])->pluck('variant.id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($summary['lines'] as $line) {
                $variant = $variants->get($line['variant']?->id);
                $product = $variant?->product;

                if (! $variant || ! $product || $product->trashed() || ! $product->is_published) {
                    throw ValidationException::withMessages(['cart' => ($line['product']?->name ?? 'Un produit').' n’est plus en vente. Retirez-le du panier pour continuer.']);
                }

                if ($variant->stock < $line['quantity']) {
                    throw ValidationException::withMessages(['cart' => $variant->displayName().' : '.($variant->stock > 0 ? 'il n’en reste que '.$variant->stock.'.' : 'ce produit vient d’être épuisé.')]);
                }
            }

            $coupon = $summary['coupon'] ? Coupon::query()->lockForUpdate()->find($summary['coupon']->id) : null;
            $discount = 0;

            if ($coupon && ! $coupon->problemFor($summary['subtotal'])) {
                $discount = $coupon->discountFor($summary['subtotal']);
                $coupon->increment('used_count');
            } else {
                $coupon = null;
            }

            $online = $paymentMethod === 'online';

            $order = Order::create([
                'number' => Order::generateNumber(),
                'access_token' => Str::random(40),
                'user_id' => $user?->id,
                'email' => mb_strtolower($customer['email']),
                'first_name' => $customer['first_name'],
                'last_name' => $customer['last_name'],
                'phone' => $customer['phone'],
                'city' => $customer['city'],
                'district' => $customer['district'] ?? null,
                'address_details' => $customer['address_details'] ?? null,
                'customer_note' => $customer['customer_note'] ?? null,
                'status' => $online ? 'pending_payment' : 'new',
                'payment_method' => $paymentMethod,
                'payment_status' => $online ? 'unpaid' : 'to_collect',
                'subtotal' => $summary['subtotal'],
                'discount' => $discount,
                'total' => $summary['subtotal'] - $discount,
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'expires_at' => $online ? now()->addHours((int) Setting::get('order_expiry_hours', '24')) : null,
            ]);

            foreach ($summary['lines'] as $line) {
                $variant = $variants->get($line['variant']->id);

                $order->items()->create([
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_name' => $variant->name,
                    'sku' => $variant->sku,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                ]);

                $this->stock->move($variant, -$line['quantity'], 'reservation', $order, $user);
            }

            $order->record('created', $user, ['to_status' => $order->status]);
            $this->carts->clear($cart);

            return $order;
        });

        $order->load('items.variant.product');
        $order->items->each(fn ($item) => $item->variant && $this->stock->checkLowStock($item->variant));

        $this->notifyCustomer($order, new OrderPlaced($order));

        if ($order->payment_method === 'on_delivery') {
            ShopTeam::notify(new NewShopOrder($order), 'order_notifications');
        }

        return $order;
    }

    /** Paiement confirmé par le prestataire. Sans effet si la commande est déjà payée. */
    public function markPaid(Order $order, Payment $payment): void
    {
        $wasPaid = DB::transaction(function () use ($order, $payment) {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($locked->isPaid()) {
                return true;
            }

            $from = $locked->status;
            $reopened = $from === 'cancelled';

            if ($reopened) {
                // Paiement arrivé après l'annulation automatique : la commande est rétablie.
                $locked->loadMissing('items.variant');
                foreach ($locked->items as $item) {
                    $item->variant && $this->stock->move($item->variant, -$item->quantity, 'reservation', $locked, null, 'Paiement reçu après expiration');
                }
            }

            $locked->forceFill([
                'status' => 'paid',
                'payment_status' => 'paid',
                'paid_at' => now(),
                'cancelled_at' => null,
                'expires_at' => null,
            ])->save();

            $locked->record('paid', null, ['details' => $payment->providerLabel()]);
            $locked->record('status', null, ['from_status' => $from, 'to_status' => 'paid']);

            if ($reopened) {
                $locked->record('note', null, ['details' => 'paiement reçu après l’annulation automatique : vérifier le stock avant de préparer.']);
            }

            $order->setRawAttributes($locked->getAttributes(), true);

            return false;
        });

        if ($wasPaid) {
            return;
        }

        $this->notifyCustomer($order, new OrderPaid($order));
        ShopTeam::notify(new NewShopOrder($order), 'order_notifications');
    }

    public function advance(Order $order, User $user): void
    {
        $next = $order->nextStatus();
        abort_unless($next !== null, 422);

        $this->changeStatus($order, $next, $user);

        if ($next === 'completed') {
            $order->forceFill(['completed_at' => now()])->save();
        }

        if (in_array($next, ['preparing', 'ready', 'completed'], true)) {
            $this->notifyCustomer($order, new OrderStatusUpdated($order));
        }
    }

    public function collectCash(Order $order, User $user): void
    {
        abort_unless($order->payment_status === 'to_collect', 422);

        $order->payments()->create(['provider' => 'cash', 'amount' => $order->total, 'status' => 'collected', 'reference' => 'Encaissé par '.$user->name]);
        $order->forceFill(['payment_status' => 'paid', 'paid_at' => now()])->save();
        $order->record('collected', $user);
    }

    public function cancel(Order $order, ?User $user, ?string $reason = null, string $event = 'cancelled'): void
    {
        DB::transaction(function () use ($order, $user, $reason, $event) {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);

            if (! $locked->isOpen()) {
                return;
            }

            $from = $locked->status;
            $this->stock->release($locked, 'release', $user);

            if ($locked->coupon_id) {
                $locked->coupon()->where('used_count', '>', 0)->decrement('used_count');
            }

            $locked->forceFill(['status' => 'cancelled', 'cancelled_at' => now(), 'expires_at' => null])->save();
            $locked->record($event, $user, ['from_status' => $from, 'to_status' => 'cancelled', 'details' => $reason]);
            $locked->payments()->where('status', 'pending')->update(['status' => 'canceled']);

            $order->setRawAttributes($locked->getAttributes(), true);
        });

        $this->notifyCustomer($order, new OrderStatusUpdated($order, $reason));
    }

    public function refund(Order $order, int $amount, ?string $reason, bool $restock, User $user): OrderRefund
    {
        if ($amount < 1 || $amount > $order->refundableAmount()) {
            throw ValidationException::withMessages(['amount' => 'Le montant doit être compris entre 1 et '.money($order->refundableAmount()).'.']);
        }

        $refund = DB::transaction(function () use ($order, $amount, $reason, $restock, $user) {
            $refund = $order->refunds()->create(['user_id' => $user->id, 'amount' => $amount, 'reason' => $reason, 'restock' => $restock]);
            $refunded = $order->refunded_amount + $amount;
            $full = $refunded >= $order->total;
            $from = $order->status;

            if ($restock && $order->status !== 'cancelled') {
                $this->stock->release($order, 'refund', $user);
            }

            $order->forceFill([
                'refunded_amount' => $refunded,
                'payment_status' => $full ? 'refunded' : 'partially_refunded',
                'status' => $full ? 'refunded' : $order->status,
            ])->save();

            $order->record('refund', $user, ['details' => money($amount).($reason ? ' : '.$reason : '')]);

            if ($full && $from !== 'refunded') {
                $order->record('status', $user, ['from_status' => $from, 'to_status' => 'refunded']);
            }

            return $refund;
        });

        $this->notifyCustomer($order, new OrderStatusUpdated($order, $reason, $amount));

        return $refund;
    }

    public function notifyCustomer(Order $order, Notification $notification): void
    {
        $order->user && ! $order->user->isSuspended()
            ? $order->user->notify($notification)
            : Notifier::route('mail', $order->email)->notify($notification);
    }

    private function changeStatus(Order $order, string $to, User $user): void
    {
        $from = $order->status;
        $order->forceFill(['status' => $to])->save();
        $order->record('status', $user, ['from_status' => $from, 'to_status' => $to]);
    }
}
