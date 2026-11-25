@extends('layouts.app')

@section('title', 'Commande')
@section('noindex', true)

@php
    $field = fn (string $name) => old($name, $defaults[$name] ?? '');
    $addressData = $addresses->map(fn ($a) => [
        'id' => $a->id, 'first_name' => $a->first_name, 'last_name' => $a->last_name, 'phone' => $a->phone,
        'city' => $a->city, 'district' => $a->district, 'address_details' => $a->details,
    ])->values();
    $firstMethod = old('payment_method', array_key_first($methods));
@endphp

@section('content')
<section class="border-b border-line bg-cream/60">
    <div class="max-w-content px-site py-8">
        <h1 class="display-md">Finaliser la commande</h1>
        <x-shop.checkout-steps :current="2" class="mt-5" />
    </div>
</section>

<section class="max-w-content px-site py-10">
    @if($errors->has('cart'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            {{ $errors->first('cart') }} <a href="{{ route('shop.cart') }}" class="font-medium underline">Revenir au panier</a>
        </div>
    @endif

    <form method="POST" action="{{ route('shop.checkout.store') }}" class="grid items-start gap-8 lg:grid-cols-[1fr_22rem]"
          x-data="{ addresses: @js($addressData), sending: false,
                    fill(id) { const a = this.addresses.find(x => x.id == id); if (!a) return;
                               for (const k of ['first_name','last_name','phone','city','district','address_details']) { this.$refs[k].value = a[k] || '' } } }"
          @submit="sending = true">
        @csrf
        <div class="space-y-6">
            @guest
                <div class="rounded-xl bg-cream px-4 py-3 text-sm text-dark">
                    Vous avez un compte ? <a href="{{ route('login') }}" class="font-medium underline underline-offset-2">Connectez-vous</a> pour retrouver vos adresses et suivre vos commandes. Sinon, continuez sans compte.
                </div>
            @endguest

            <fieldset class="rounded-2xl border border-line bg-white p-5 sm:p-6">
                <legend class="sr-only">Vos coordonnées</legend>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold text-dark">1. Vos coordonnées</h2>
                    @if($addresses->isNotEmpty())
                        <div class="flex items-center gap-2">
                            <label for="address-picker" class="text-sm text-muted">Adresse enregistrée</label>
                            <select id="address-picker" class="field-input !py-1.5 text-sm" @change="fill($event.target.value)">
                                @foreach($addresses as $address)
                                    <option value="{{ $address->id }}">{{ $address->label ?: $address->city }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    @foreach([
                        ['first_name', 'Prénom', 'text', 'given-name', true],
                        ['last_name', 'Nom', 'text', 'family-name', true],
                        ['email', 'E-mail', 'email', 'email', true],
                        ['phone', 'Téléphone', 'tel', 'tel', true],
                        ['city', 'Ville', 'text', 'address-level2', true],
                        ['district', 'Quartier', 'text', 'address-level3', false],
                    ] as [$name, $label, $type, $autocomplete, $required])
                        <div>
                            <label for="{{ $name }}" class="field-label">{{ $label }}{{ $required ? ' *' : '' }}</label>
                            <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" x-ref="{{ $name }}" value="{{ $field($name) }}" autocomplete="{{ $autocomplete }}" @if($required) required @endif maxlength="{{ $name === 'email' ? 255 : 120 }}" class="field-input @error($name) border-red-400 @enderror">
                            @error($name)<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                    <div class="sm:col-span-2">
                        <label for="address_details" class="field-label">Adresse ou repère</label>
                        <input id="address_details" name="address_details" x-ref="address_details" value="{{ $field('address_details') }}" maxlength="255" autocomplete="street-address" placeholder="Rue, maison, point de repère" class="field-input">
                        @error('address_details')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="customer_note" class="field-label">Précisions pour notre équipe</label>
                        <textarea id="customer_note" name="customer_note" rows="2" maxlength="1000" class="field-input" placeholder="Disponibilités pour la remise, informations utiles…">{{ old('customer_note') }}</textarea>
                    </div>
                    @auth
                        <label class="flex items-center gap-2.5 text-sm text-dark sm:col-span-2">
                            <input type="hidden" name="save_address" value="0">
                            <input type="checkbox" name="save_address" value="1" @checked(old('save_address', $addresses->isEmpty())) class="h-4 w-4 rounded border-line text-primary-800 focus:ring-primary-500">
                            Enregistrer cette adresse dans mon compte
                        </label>
                    @endauth
                </div>
                <p class="mt-4 text-sm text-muted">Notre équipe vous contacte à ce numéro pour convenir de la remise de votre commande.</p>
            </fieldset>

            <fieldset class="rounded-2xl border border-line bg-white p-5 sm:p-6">
                <legend class="sr-only">Mode de paiement</legend>
                <h2 class="text-lg font-semibold text-dark">2. Paiement</h2>
                @if(empty($methods))
                    <p class="mt-4 text-sm text-red-700">Aucun mode de paiement n’est disponible pour le moment. Contactez-nous pour commander.</p>
                @else
                    <div class="mt-4 grid gap-3">
                        @foreach($methods as $value => $method)
                            <label class="flex cursor-pointer gap-3 rounded-xl border border-line p-4 has-[:checked]:border-primary-800 has-[:checked]:bg-primary-50/50">
                                <input type="radio" name="payment_method" value="{{ $value }}" @checked($firstMethod === $value) required class="mt-1 h-4 w-4 border-line text-primary-800 focus:ring-primary-500">
                                <span>
                                    <span class="block font-medium text-dark">{{ $method['label'] }}</span>
                                    <span class="mt-0.5 block text-sm leading-6 text-muted">{{ $method['help'] }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('payment_method')<p class="field-error">{{ $message }}</p>@enderror
                @endif
            </fieldset>
        </div>

        <aside class="space-y-4 lg:sticky lg:top-24">
            <div class="rounded-2xl border border-line bg-white p-5">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-dark">Votre commande</h2>
                    <a href="{{ route('shop.cart') }}" class="text-sm text-muted hover:text-dark">Modifier</a>
                </div>
                <ul class="mt-4 space-y-3">
                    @foreach($summary['lines'] as $line)
                        <li class="flex gap-3 text-sm">
                            <span class="relative h-12 w-12 shrink-0 overflow-hidden rounded-lg bg-cream">
                                @if($line['product']?->coverUrl())<img src="{{ $line['product']->coverUrl() }}" alt="" class="h-full w-full object-cover">@endif
                                <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-dark px-1 text-[11px] font-semibold text-white">{{ $line['quantity'] }}</span>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate font-medium text-dark">{{ $line['product']?->name }}</span>
                                @if($line['variant']?->name)<span class="block text-muted">{{ $line['variant']->name }}</span>@endif
                            </span>
                            <span class="text-dark">{{ money($line['line_total']) }}</span>
                        </li>
                    @endforeach
                </ul>
                @include('shop.partials.totals', ['summary' => $summary])

                <label class="mt-5 flex gap-2.5 text-sm leading-6 text-dark">
                    <input type="checkbox" name="cgv" value="1" required @checked(old('cgv')) class="mt-1 h-4 w-4 rounded border-line text-primary-800 focus:ring-primary-500">
                    <span>J’accepte les <a href="{{ route('cgv') }}" target="_blank" class="underline underline-offset-2">conditions générales de vente</a>.</span>
                </label>
                @error('cgv')<p class="field-error">{{ $message }}</p>@enderror

                <button type="submit" class="btn-primary mt-5 w-full" :disabled="sending" @disabled(empty($methods))>
                    <span x-text="sending ? 'Validation…' : 'Valider et payer {{ money($summary['total']) }}'">Valider et payer {{ money($summary['total']) }}</span>
                </button>
                <p class="mt-3 text-center text-xs text-muted">Paiement sécurisé : AgriFish ne conserve aucune donnée bancaire.</p>
            </div>
        </aside>
    </form>
</section>
@endsection
