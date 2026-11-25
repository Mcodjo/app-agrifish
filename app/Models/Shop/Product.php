<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'reference', 'summary', 'description', 'technical_sheet_path',
        'sale_starts_at', 'sale_ends_at', 'low_stock_threshold', 'is_published', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'sale_starts_at' => 'datetime',
            'sale_ends_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order')->orderBy('id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('status', 'approved')->latest();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function isOnSale(): bool
    {
        $now = now();

        return ($this->sale_starts_at === null || $this->sale_starts_at->lte($now))
            && ($this->sale_ends_at === null || $this->sale_ends_at->gt($now))
            && ($this->sale_starts_at !== null || $this->sale_ends_at !== null);
    }

    public function totalStock(): int
    {
        return (int) $this->variants->sum(fn (ProductVariant $variant) => max(0, $variant->stock));
    }

    public function isAvailable(): bool
    {
        return $this->totalStock() > 0;
    }

    public function fromPrice(): int
    {
        return (int) ($this->variants->map->currentPrice()->min() ?? 0);
    }

    public function fromRegularPrice(): int
    {
        return (int) ($this->variants->min('price') ?? 0);
    }

    public function hasChoice(): bool
    {
        return $this->variants->count() > 1;
    }

    public function coverUrl(): ?string
    {
        $image = $this->images->first();

        return $image ? $image->url() : null;
    }

    public function averageRating(): ?float
    {
        $avg = $this->approvedReviews()->avg('rating');

        return $avg ? round((float) $avg, 1) : null;
    }

    public function technicalSheetUrl(): ?string
    {
        return $this->technical_sheet_path ? Storage::disk('public')->url($this->technical_sheet_path) : null;
    }
}
