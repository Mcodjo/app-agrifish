<?php

namespace App\Notifications;

use App\Support\TwoFactorCode;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCodeNotification extends Notification
{
    use Queueable;

    public function __construct(public string $code)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre code de connexion AgriFish : '.$this->code)
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Voici votre code de connexion :')
            ->line('**'.$this->code.'**')
            ->line('Il est valable '.TwoFactorCode::TTL_MINUTES.' minutes. Si vous n’êtes pas à l’origine de cette connexion, changez votre mot de passe.')
            ->salutation('L’équipe AgriFish');
    }
}
