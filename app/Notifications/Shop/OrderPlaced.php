<?php

namespace App\Notifications\Shop;

use App\Models\Setting;
use App\Models\Shop\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification
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
        $mail = (new MailMessage)
            ->subject('Commande '.$order->number.' enregistrée')
            ->greeting('Bonjour '.$order->first_name.',')
            ->line('Merci pour votre commande. Voici son récapitulatif :');

        foreach ($order->items as $item) {
            $mail->line('• '.$item->quantity.' × '.$item->label().' : '.money($item->line_total));
        }

        if ($order->discount > 0) {
            $mail->line('Réduction ('.$order->coupon_code.') : -'.money($order->discount));
        }

        $mail->line('**Total : '.money($order->total).'**');

        if ($order->payment_method === 'online') {
            $mail->line('Votre commande sera confirmée dès réception du paiement. Sans paiement sous '.Setting::get('order_expiry_hours', '24').' heures, elle est annulée automatiquement.')
                ->action('Suivre ou payer ma commande', $order->publicUrl());
        } else {
            $mail->line('Vous réglerez '.money($order->total).' à la remise de la commande. '.Setting::get('pickup_instructions'))
                ->action('Suivre ma commande', $order->publicUrl());
        }

        return $mail->salutation('L’équipe AgriFish');
    }
}
