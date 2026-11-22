<?php

namespace App\Notifications;

use App\Models\ProjectMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewProjectMessage extends Notification
{
    use Queueable;

    public function __construct(public ProjectMessage $message)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $project = $this->message->serviceRequest;
        $sender = $this->message->sender?->name ?? 'AgriFish';

        return (new MailMessage)
            ->subject('Nouveau message sur le projet '.$project->reference)
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line($sender.' vous a écrit au sujet du projet « '.$project->title.' » :')
            ->line('« '.Str::limit($this->message->body, 400).' »')
            ->lineIf((bool) $this->message->attachment_path, 'Le message contient une pièce jointe.')
            ->action('Lire et répondre', route('projects.show', $project).'#messagerie')
            ->line('Vous pouvez désactiver ces e-mails dans votre profil.')
            ->salutation('L’équipe AgriFish');
    }
}
