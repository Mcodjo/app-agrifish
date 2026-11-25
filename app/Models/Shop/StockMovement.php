<?php

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    public const UPDATED_AT = null;

    public const TYPES = [
        'reservation' => 'Commande',
        'release' => 'Commande annulée',
        'restock' => 'Entrée en stock',
        'adjustment' => 'Correction',
        'refund' => 'Retour',
        'import' => 'Import',
    ];

    protected $fillable = ['product_variant_id', 'order_id', 'user_id', 'quantity', 'stock_after', 'type', 'note'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
