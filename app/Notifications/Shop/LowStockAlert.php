<?php

namespace App\Notifications\Shop;

use App\Models\Shop\ProductVariant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification
{
    use Queueable;

    public function __construct(public ProductVariant $variant)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $product = $this->variant->product;

        return (new MailMessage)
            ->subject('Stock bas : '.$this->variant->displayName())
            ->greeting('Bonjour,')
            ->line('Il reste '.max(0, $this->variant->stock).' exemplaire(s) de « '.$this->variant->displayName().' » (référence '.$this->variant->sku.').')
            ->line('Seuil d’alerte : '.$product->low_stock_threshold.'.')
            ->action('Mettre à jour le stock', route('shop-admin.products.edit', $product))
            ->salutation('AgriFish');
    }
}
