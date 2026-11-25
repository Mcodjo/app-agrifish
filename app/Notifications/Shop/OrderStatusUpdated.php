<?php

namespace App\Notifications\Shop;

use App\Channels\SmsChannel;
use App\Models\Setting;
use App\Models\Shop\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public ?string $reason = null, public ?int $refundAmount = null)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['mail'];

        if ($this->order->status === 'ready' && $this->refundAmount === null && Setting::enabled('sms_on_ready')) {
            $channels[] = SmsChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order;
        $mail = (new MailMessage)->greeting('Bonjour '.$order->first_name.',');

        if ($this->refundAmount !== null) {
            $mail->subject('Remboursement de la commande '.$order->number)
                ->line('Nous avons procédé au remboursement de '.money($this->refundAmount).' sur votre commande.')
                ->lineIf((bool) $this->reason, 'Motif : '.$this->reason)
                ->line('Le délai de réception dépend de votre moyen de paiement.');
        } else {
            $mail->subject('Commande '.$order->number.' : '.mb_strtolower($order->statusLabel()))
                ->line(match ($order->status) {
                    'preparing' => 'Votre commande est en cours de préparation.',
                    'ready' => 'Votre commande est prête. '.Setting::get('pickup_instructions'),
                    'completed' => 'Votre commande vous a été remise. Merci pour votre confiance.',
                    'cancelled' => 'Votre commande a été annulée.',
                    default => 'Le statut de votre commande est maintenant : '.$order->statusLabel().'.',
                })
                ->lineIf($order->status === 'cancelled' && (bool) $this->reason, 'Motif : '.$this->reason)
                ->lineIf($order->status === 'cancelled' && $order->isPaid(), 'Le remboursement de votre paiement vous sera confirmé par e-mail.');
        }

        return $mail->action('Voir ma commande', $order->publicUrl())->salutation('L’équipe AgriFish');
    }

    public function toSms(object $notifiable): string
    {
        return 'AgriFish : votre commande '.$this->order->number.' est prête. '.Setting::get('pickup_instructions');
    }

    public function smsRecipient(object $notifiable): ?string
    {
        return $this->order->phone;
    }
}
