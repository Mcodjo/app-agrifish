<?php

namespace App\Services\Shop;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as Notifier;

/** Destinataires des alertes de la boutique : gestionnaires et administrateurs actifs, plus l'adresse des réglages. */
class ShopTeam
{
    public static function notify(Notification $notification, string $preference): void
    {
        $members = User::query()
            ->whereIn('role', ['manager', 'admin'])
            ->whereNull('suspended_at')
            ->get()
            ->filter(fn (User $user) => $user->wantsEmail($preference));

        Notifier::send($members, $notification);

        $extra = trim((string) Setting::get('shop_notification_email'));

        if ($extra !== '' && filter_var($extra, FILTER_VALIDATE_EMAIL) && ! $members->contains('email', $extra)) {
            Notifier::route('mail', $extra)->notify($notification);
        }
    }
}
