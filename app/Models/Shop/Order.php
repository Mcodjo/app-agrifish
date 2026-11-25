<?php

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    public const STATUSES = [
        'pending_payment' => 'En attente de paiement',
        'new' => 'Nouvelle',
        'paid' => 'Payée',
        'preparing' => 'En préparation',
        'ready' => 'Prête',
        'completed' => 'Remise',
        'cancelled' => 'Annulée',
        'refunded' => 'Remboursée',
    ];

    public const PAYMENT_STATUSES = [
        'unpaid' => 'Non payée',
        'to_collect' => 'À encaisser',
        'paid' => 'Payée',
        'partially_refunded' => 'Remboursée en partie',
        'refunded' => 'Remboursée',
    ];

    public const PAYMENT_METHODS = [
        'online' => 'Mobile Money ou carte',
        'on_delivery' => 'Paiement à la remise',
    ];

    /** Étapes suivantes possibles, dans l'ordre de traitement. */
    public const NEXT = [
        'new' => 'preparing',
        'paid' => 'preparing',
        'preparing' => 'ready',
        'ready' => 'completed',
    ];

    public const OPEN = ['pending_payment', 'new', 'paid', 'preparing', 'ready'];

    protected $fillable = [
        'number', 'access_token', 'user_id', 'email', 'first_name', 'last_name', 'phone', 'city', 'district',
        'address_details', 'customer_note', 'status', 'payment_method', 'payment_status', 'subtotal', 'discount',
        'total', 'refunded_amount', 'coupon_id', 'coupon_code', 'expires_at', 'paid_at', 'completed_at', 'cancelled_at',
    ];

    protected $hidden = ['access_token'];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'discount' => 'integer',
            'total' => 'integer',
            'refunded_amount' => 'integer',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'number';
    }

    public static function generateNumber(): string
    {
        do {
            $number = 'CMD-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (static::query()->where('number', $number)->exists());

        return $number;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class)->orderBy('id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest('id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(OrderRefund::class)->latest('id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class)->latest('created_at')->latest('id');
    }

    public function record(string $type, ?User $user = null, array $attributes = []): OrderEvent
    {
        return $this->events()->create(array_merge(['type' => $type, 'user_id' => $user?->id], $attributes));
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function paymentStatusLabel(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? $this->payment_status;
    }

    public function paymentMethodLabel(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    public function customerName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function addressLine(): string
    {
        return collect([$this->address_details, $this->district, $this->city])->filter()->implode(', ');
    }

    public function isOpen(): bool
    {
        return in_array($this->status, self::OPEN, true);
    }

    public function isPaid(): bool
    {
        return in_array($this->payment_status, ['paid', 'partially_refunded', 'refunded'], true);
    }

    public function canRetryPayment(): bool
    {
        return $this->status === 'pending_payment' && $this->payment_method === 'online'
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function refundableAmount(): int
    {
        return $this->isPaid() ? max(0, $this->total - $this->refunded_amount) : 0;
    }

    public function nextStatus(): ?string
    {
        return self::NEXT[$this->status] ?? null;
    }

    public function hasInvoice(): bool
    {
        return $this->isPaid() || $this->status === 'completed';
    }

    /** Lien de consultation sans compte, transmis dans les e-mails. */
    public function publicUrl(): string
    {
        return route('shop.orders.show', ['order' => $this->number, 'cle' => $this->access_token]);
    }
}
