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
            ->line('Référence: ' . $this->serviceRequest->reference)
            ->line('Nouveau statut: ' . $this->labelForStatus($this->serviceRequest->status))
            ->lineWhen((bool) $this->serviceRequest->status_notes, 'Note: ' . $this->serviceRequest->status_notes)
            ->action('Voir votre espace client', route('dashboard'))
            ->line('Merci de faire confiance à AgriFish.');
    }

    private function labelForStatus(string $status): string
    {
        return match ($status) {
            'new' => 'Nouvelle',
            'reviewing' => 'En revue',
            'assigned' => 'Assignée',
            'in_progress' => 'En cours',
            'waiting_client' => 'En attente client',
            'completed' => 'Terminée',
            'closed' => 'Clôturée',
            default => ucfirst($status),
        };
    }
}
