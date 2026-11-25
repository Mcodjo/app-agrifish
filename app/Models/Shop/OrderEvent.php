<?php

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['order_id', 'user_id', 'type', 'from_status', 'to_status', 'details'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function summary(): string
    {
        $label = fn (?string $status) => Order::STATUSES[$status] ?? $status;

        return match ($this->type) {
            'created' => 'Commande passée',
            'status' => 'Statut : '.$label($this->from_status).' → '.$label($this->to_status),
            'paid' => 'Paiement reçu'.($this->details ? ' ('.$this->details.')' : ''),
            'payment_failed' => 'Paiement non abouti',
            'collected' => 'Paiement encaissé à la remise',
            'cancelled' => 'Commande annulée'.($this->details ? ' : '.$this->details : ''),
            'expired' => 'Commande annulée : paiement non reçu sous 24 heures',
            'refund' => 'Remboursement de '.$this->details,
            'note' => 'Note : '.$this->details,
            default => ucfirst($this->type),
        };
    }
}
