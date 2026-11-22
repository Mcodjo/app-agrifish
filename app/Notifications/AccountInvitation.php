<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountInvitation extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]);
        $expire = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Votre compte AgriFish')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Un compte '.mb_strtolower($notifiable->roleLabel()).' a été créé pour vous sur la plateforme AgriFish.')
            ->line('Choisissez votre mot de passe pour vous connecter.')
            ->action('Choisir mon mot de passe', $url)
            ->line('Ce lien est valable '.$expire.' minutes. Passé ce délai, utilisez « Mot de passe oublié » sur la page de connexion.')
            ->salutation('L’équipe AgriFish');
    }
}
