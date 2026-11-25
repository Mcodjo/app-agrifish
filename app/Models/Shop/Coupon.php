<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'min_amount', 'starts_at', 'ends_at', 'max_uses', 'used_count', 'is_active'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
            'value' => 'integer',
            'min_amount' => 'integer',
        ];
    }

    public function setCodeAttribute(string $value): void
    {
        $this->attributes['code'] = mb_strtoupper(trim($value));
    }

    /** Message d'erreur si le code ne peut pas s'appliquer à ce montant, sinon null. */
    public function problemFor(int $subtotal): ?string
    {
        $now = now();

        return match (true) {
            ! $this->is_active => 'Ce code promo n’est plus actif.',
            $this->starts_at && $this->starts_at->gt($now) => 'Ce code promo n’est pas encore valable.',
            $this->ends_at && $this->ends_at->lte($now) => 'Ce code promo a expiré.',
            $this->max_uses !== null && $this->used_count >= $this->max_uses => 'Ce code promo a atteint son nombre d’utilisations.',
            $subtotal < $this->min_amount => 'Ce code promo s’applique à partir de '.money($this->min_amount).' d’achat.',
            default => null,
        };
    }

    public function discountFor(int $subtotal): int
    {
        $discount = $this->type === 'percent'
            ? intdiv($subtotal * min(100, $this->value), 100)
            : $this->value;

        return min($subtotal, $discount);
    }

    public function describe(): string
    {
        return $this->type === 'percent' ? '-'.$this->value.' %' : '-'.money($this->value);
    }
}
