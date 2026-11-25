<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const STATUSES = [
        'pending' => 'En attente',
        'approved' => 'Accepté',
        'declined' => 'Refusé',
        'canceled' => 'Annulé',
        'collected' => 'Encaissé à la remise',
    ];

    public const PROVIDERS = [
        'fedapay' => 'FedaPay',
        'simulation' => 'Paiement de test',
        'cash' => 'À la remise',
    ];

    protected $fillable = ['order_id', 'provider', 'reference', 'amount', 'status', 'checkout_url', 'payload'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'amount' => 'integer'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function providerLabel(): string
    {
        return self::PROVIDERS[$this->provider] ?? $this->provider;
    }
}
