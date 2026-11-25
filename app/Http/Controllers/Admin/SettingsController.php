<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Shop\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(PaymentService $payments): View
    {
        return view('admin.settings', [
            'settings' => collect(array_keys(Setting::DEFAULTS))->mapWithKeys(fn ($key) => [$key => Setting::get($key)]),
            'driver' => config('shop.payment_driver'),
            'onlineConfigured' => $payments->onlineAvailable() || ! Setting::enabled('online_payment'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_expiry_hours' => ['required', 'integer', 'between:1,168'],
            'shop_notification_email' => ['nullable', 'email', 'max:255'],
            'pickup_instructions' => ['required', 'string', 'max:500'],
        ]);

        if (! $request->boolean('online_payment') && ! $request->boolean('cash_on_delivery')) {
            return back()->withErrors(['payment' => 'Activez au moins un mode de paiement.'])->withInput();
        }

        Setting::put([
            'shop_open' => $request->boolean('shop_open') ? '1' : '0',
            'online_payment' => $request->boolean('online_payment') ? '1' : '0',
            'cash_on_delivery' => $request->boolean('cash_on_delivery') ? '1' : '0',
            'sms_on_ready' => $request->boolean('sms_on_ready') ? '1' : '0',
            'order_expiry_hours' => (string) $validated['order_expiry_hours'],
            'shop_notification_email' => (string) ($validated['shop_notification_email'] ?? ''),
            'pickup_instructions' => $validated['pickup_instructions'],
        ]);

        return back()->with('status', 'settings-saved');
    }
}
