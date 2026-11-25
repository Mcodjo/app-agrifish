<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'name', 'sku', 'price', 'sale_price', 'stock', 'sort_order', 'low_stock_notified_at'];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'sale_price' => 'integer',
            'stock' => 'integer',
            'low_stock_notified_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest('created_at')->latest('id');
    }

    /** Prix appliqué maintenant : prix barré seulement pendant la période de promotion. */
    public function currentPrice(): int
    {
        if ($this->sale_price !== null && $this->sale_price < $this->price && $this->product?->isOnSale()) {
            return $this->sale_price;
        }

        return $this->price;
    }

    public function isDiscounted(): bool
    {
        return $this->currentPrice() < $this->price;
    }

    public function label(): string
    {
        return $this->name ?: 'Format unique';
    }

    public function displayName(): string
    {
        return trim($this->product->name.($this->name ? ' – '.$this->name : ''));
    }
}
