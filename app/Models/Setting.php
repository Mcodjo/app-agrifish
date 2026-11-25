<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    public const DEFAULTS = [
        'shop_open' => '1',
        'cash_on_delivery' => '1',
        'online_payment' => '1',
        'order_expiry_hours' => '24',
        'shop_notification_email' => '',
        'pickup_instructions' => 'Nous vous appelons dès que votre commande est prête pour convenir de sa remise.',
        'sms_on_ready' => '0',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        $all = Cache::rememberForever('settings.all', fn () => static::query()->pluck('value', 'key')->all());

        return $all[$key] ?? $default ?? (self::DEFAULTS[$key] ?? null);
    }

    public static function enabled(string $key): bool
    {
        return (bool) (int) static::get($key);
    }

    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget('settings.all');
    }
}
