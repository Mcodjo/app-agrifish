@extends('layouts.espace')

@section('title', 'Réglages')

@section('content')
<x-espace.header title="Réglages de la boutique" subtitle="Ouverture, moyens de paiement et informations transmises aux clients." />

<x-espace.body>
    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-3xl space-y-6">
        @csrf @method('PUT')
        @error('payment')<div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</div>@enderror

        <x-espace.section title="Boutique">
            @foreach([
                'shop_open' => ['Boutique ouverte', 'Fermée, la boutique reste visible mais les commandes sont suspendues.'],
                'online_payment' => ['Paiement en ligne', 'Mobile Money et carte bancaire via le prestataire de paiement.'],
                'cash_on_delivery' => ['Paiement à la remise', 'Le client règle au moment où il reçoit sa commande.'],
                'sms_on_ready' => ['SMS « commande prête »', 'Nécessite un fournisseur SMS configuré (SMS_DRIVER=http).'],
            ] as $key => [$label, $help])
                <label class="flex items-start justify-between gap-4 border-b border-line py-3 first:pt-0 last:border-0 last:pb-0">
                    <span><span class="block font-medium text-dark">{{ $label }}</span><span class="text-sm text-muted">{{ $help }}</span></span>
                    <input type="hidden" name="{{ $key }}" value="0">
                    <input type="checkbox" name="{{ $key }}" value="1" @checked(old($key, $settings[$key])) class="mt-1 h-5 w-5 rounded border-line text-primary-800">
                </label>
            @endforeach
            @unless($onlineConfigured)
                <p class="mt-4 rounded-lg bg-amber-50 px-3 py-2 text-sm text-amber-900">Le paiement en ligne est activé mais pas opérationnel : renseignez SHOP_PAYMENT_DRIVER=fedapay et FEDAPAY_SECRET_KEY dans le fichier .env du serveur.</p>
            @endunless
            <p class="mt-4 text-xs text-muted">Prestataire de paiement configuré : {{ $driver === 'fedapay' ? 'FedaPay' : 'paiement de test (hors production)' }}.</p>
        </x-espace.section>

        <x-espace.section title="Commandes">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="order_expiry_hours" class="field-label">Délai de paiement (heures)</label>
                    <input id="order_expiry_hours" type="number" name="order_expiry_hours" min="1" max="168" value="{{ old('order_expiry_hours', $settings['order_expiry_hours']) }}" class="field-input">
                    <p class="mt-1 text-xs text-muted">Passé ce délai, une commande impayée est annulée et son stock libéré.</p>
                </div>
                <div>
                    <label for="shop_notification_email" class="field-label">E-mail supplémentaire pour les alertes</label>
                    <input id="shop_notification_email" type="email" name="shop_notification_email" value="{{ old('shop_notification_email', $settings['shop_notification_email']) }}" class="field-input" placeholder="boutique@exemple.com">
                    @error('shop_notification_email')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="pickup_instructions" class="field-label">Message sur la remise des commandes</label>
                    <textarea id="pickup_instructions" name="pickup_instructions" rows="3" maxlength="500" class="field-input">{{ old('pickup_instructions', $settings['pickup_instructions']) }}</textarea>
                    <p class="mt-1 text-xs text-muted">Affiché au client à la commande et dans les e-mails.</p>
                </div>
            </div>
        </x-espace.section>

        <div class="flex justify-end"><button type="submit" class="btn-primary">Enregistrer les réglages</button></div>
    </form>
</x-espace.body>
@endsection
