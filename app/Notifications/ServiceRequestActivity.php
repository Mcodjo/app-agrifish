<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Prévient l'équipe d'une demande déposée ou annulée par un client.
 */
class ServiceRequestActivity extends Notification
{
    use Queueable;

    public function __construct(public ServiceRequest $serviceRequest, public string $type)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $project = $this->serviceRequest;
        $client = $project->client?->name ?? 'Un client';

        $mail = (new MailMessage)->greeting('Bonjour '.$notifiable->name.',');

        if ($this->type === 'cancelled') {
            return $mail
                ->subject('Demande annulée : '.$project->reference)
                ->line($client.' a annulé la demande « '.$project->title.' ».')
                ->action('Voir la demande', route('projects.show', $project))
                ->salutation('AgriFish');
        }

        return $mail
            ->subject('Nouvelle demande : '.$project->reference)
            ->line($client.' a déposé une demande « '.$project->title.' ».')
            ->line('Domaine : '.(ServiceRequest::SERVICE_TYPES[$project->service_type] ?? $project->service_type)
                .' · Priorité : '.(['low' => 'faible', 'normal' => 'normale', 'high' => 'haute'][$project->priority] ?? $project->priority))
            ->action('Analyser et assigner', route('admin.requests.index', ['q' => $project->reference]))
            ->salutation('AgriFish');
    }
}
