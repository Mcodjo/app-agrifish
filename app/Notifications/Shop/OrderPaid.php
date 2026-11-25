<?php

namespace App\Notifications\Shop;

use App\Models\Setting;
use App\Models\Shop\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPaid extends Notification
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
        return (new MailMessage)
            ->subject('Paiement reçu pour la commande '.$this->order->number)
            ->greeting('Bonjour '.$this->order->first_name.',')
            ->line('Nous avons bien reçu votre paiement de '.money($this->order->total).'. Votre commande est confirmée.')
            ->line(Setting::get('pickup_instructions'))
            ->action('Voir ma commande et ma facture', $this->order->publicUrl())
            ->salutation('L’équipe AgriFish');
    }
}
