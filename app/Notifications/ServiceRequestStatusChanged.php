<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceRequestStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public ServiceRequest $serviceRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Mise à jour de votre demande ' . $this->serviceRequest->reference)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Le statut de votre demande a changé.')
            ->line('Référence : ' . $this->serviceRequest->reference)
            ->line('Nouveau statut : ' . $this->serviceRequest->statusLabel())
            ->lineIf((bool) $this->serviceRequest->status_notes, 'Note de l’équipe : ' . $this->serviceRequest->status_notes)
            ->action('Voir votre projet', route('projects.show', $this->serviceRequest))
            ->salutation('L’équipe AgriFish');
    }
}
