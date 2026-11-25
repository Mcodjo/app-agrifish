<?php

namespace App\Services\Shop;

use App\Models\Shop\Order;
use App\Models\Shop\ProductVariant;
use App\Models\Shop\StockMovement;
use App\Models\User;
use App\Notifications\Shop\LowStockAlert;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Ajoute (quantité positive) ou retire (négative) du stock et trace le mouvement.
     * À appeler dans une transaction quand plusieurs écritures doivent rester cohérentes.
     */
    public function move(ProductVariant $variant, int $quantity, string $type, ?Order $order = null, ?User $user = null, ?string $note = null): StockMovement
    {
        return DB::transaction(function () use ($variant, $quantity, $type, $order, $user, $note) {
            $locked = ProductVariant::query()->lockForUpdate()->findOrFail($variant->id);
            $locked->stock += $quantity;

            if ($locked->stock > ($locked->product?->low_stock_threshold ?? 0)) {
                $locked->low_stock_notified_at = null;
            }

            $locked->save();
            $variant->setRawAttributes($locked->getAttributes(), true);

            return StockMovement::create([
                'product_variant_id' => $locked->id,
                'order_id' => $order?->id,
                'user_id' => $user?->id,
                'quantity' => $quantity,
                'stock_after' => $locked->stock,
                'type' => $type,
                'note' => $note,
            ]);
        });
    }

    public function release(Order $order, string $type = 'release', ?User $user = null): void
    {
        $order->loadMissing('items.variant');

        foreach ($order->items as $item) {
            if ($item->variant) {
                $this->move($item->variant, $item->quantity, $type, $order, $user);
            }
        }
    }

    /** Prévient l'équipe une seule fois tant que le stock reste sous le seuil. */
    public function checkLowStock(ProductVariant $variant): void
    {
        $variant->refresh()->loadMissing('product');
        $threshold = $variant->product?->low_stock_threshold ?? 0;

        if ($variant->stock > $threshold || $variant->low_stock_notified_at !== null) {
            return;
        }

        $variant->forceFill(['low_stock_notified_at' => now()])->save();

        ShopTeam::notify(new LowStockAlert($variant), 'stock_notifications');
    }
}
