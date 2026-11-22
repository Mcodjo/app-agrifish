<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.agrifish');

        ResetPassword::toMailUsing(function (object $notifiable, string $token): MailMessage {
            $url = route('password.reset', ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()]);
            $expire = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

            return (new MailMessage)
                ->subject('Réinitialisation de votre mot de passe')
                ->greeting('Bonjour '.$notifiable->name.',')
                ->line('Vous recevez cet e-mail car une réinitialisation du mot de passe a été demandée pour votre compte AgriFish.')
                ->action('Choisir un nouveau mot de passe', $url)
                ->line('Ce lien est valable '.$expire.' minutes.')
                ->line('Si vous n’êtes pas à l’origine de cette demande, ignorez ce message : votre mot de passe reste inchangé.')
                ->salutation('L’équipe AgriFish');
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url): MailMessage {
            return (new MailMessage)
                ->subject('Confirmez votre adresse e-mail')
                ->greeting('Bonjour '.$notifiable->name.',')
                ->line('Merci de votre inscription sur AgriFish. Confirmez votre adresse e-mail pour activer votre espace.')
                ->action('Confirmer mon adresse', $url)
                ->line('Si vous n’avez pas créé de compte, ignorez ce message.')
                ->salutation('L’équipe AgriFish');
        });
    }
}
