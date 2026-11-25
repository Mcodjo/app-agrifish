<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * SMS transactionnels. Pilote « log » par défaut ; « http » envoie un POST JSON
 * { to, message, sender } au fournisseur configuré (SMS_HTTP_URL, jeton SMS_HTTP_TOKEN).
 */
class SmsChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $to = method_exists($notification, 'smsRecipient') ? $notification->smsRecipient($notifiable) : ($notifiable->phone ?? null);

        if (! $to || ! method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);
        $config = config('services.sms');

        if (($config['driver'] ?? 'log') !== 'http' || empty($config['url'])) {
            Log::info('SMS', ['to' => $to, 'message' => $message]);

            return;
        }

        try {
            Http::withToken((string) $config['token'])->timeout(10)
                ->post($config['url'], ['to' => $to, 'message' => $message, 'sender' => $config['sender']])
                ->throw();
        } catch (Throwable $e) {
            Log::warning('Échec d’envoi SMS', ['to' => $to, 'error' => $e->getMessage()]);
        }
    }
}
