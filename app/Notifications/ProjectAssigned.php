<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectAssigned extends Notification
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
        $project = $this->serviceRequest;

        return (new MailMessage)
            ->subject('Projet assigné : '.$project->reference)
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Le projet « '.$project->title.' » vous a été confié.')
            ->line('Client : '.($project->client?->name ?? '—'))
            ->line('Domaine : '.(ServiceRequest::SERVICE_TYPES[$project->service_type] ?? $project->service_type))
            ->action('Ouvrir le projet', route('projects.show', $project))
            ->line('Pensez à planifier les étapes pour que le client puisse suivre l’avancement.')
            ->salutation('L’équipe AgriFish');
    }
}
