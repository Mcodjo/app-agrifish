<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Support\Facades\Cache;

/** Code de connexion à 6 chiffres, valable 10 minutes, 5 essais au plus. */
class TwoFactorCode
{
    public const TTL_MINUTES = 10;

    public const MAX_ATTEMPTS = 5;

    public static function send(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        Cache::put(self::key($user->id), hash('sha256', $code), now()->addMinutes(self::TTL_MINUTES));
        Cache::forget(self::attemptsKey($user->id));

        $user->notify(new TwoFactorCodeNotification($code));
    }

    public static function verify(int $userId, string $code): bool
    {
        $hash = Cache::get(self::key($userId));

        if (! $hash) {
            return false;
        }

        $attempts = Cache::increment(self::attemptsKey($userId));

        if ($attempts > self::MAX_ATTEMPTS) {
            Cache::forget(self::key($userId));

            return false;
        }

        if (! hash_equals($hash, hash('sha256', $code))) {
            return false;
        }

        Cache::forget(self::key($userId));
        Cache::forget(self::attemptsKey($userId));

        return true;
    }

    private static function key(int $userId): string
    {
        return 'two_factor_code:'.$userId;
    }

    private static function attemptsKey(int $userId): string
    {
        return 'two_factor_attempts:'.$userId;
    }
}
