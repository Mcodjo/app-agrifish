<?php

namespace App\Notifications\Shop;

use App\Models\Shop\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewShopOrder extends Notification
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order->loadMissing('items');
        $paid = $order->payment_status === 'paid';

        $mail = (new MailMessage)
            ->subject(($paid ? 'Commande payée' : 'Nouvelle commande à encaisser').' : '.$order->number.' ('.money($order->total).')')
            ->greeting('Bonjour,')
            ->line($order->customerName().' ('.$order->phone.', '.$order->city.') a passé une commande de '.money($order->total).'.')
            ->line($paid ? 'Le paiement en ligne est confirmé.' : 'Paiement prévu à la remise de la commande.');

        foreach ($order->items as $item) {
            $mail->line('• '.$item->quantity.' × '.$item->label());
        }

        return $mail->action('Traiter la commande', route('shop-admin.orders.show', $order))->salutation('AgriFish');
    }
}
