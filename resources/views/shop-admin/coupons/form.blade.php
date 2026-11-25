@extends('layouts.espace')

@php
    $editing = $coupon->exists;
    $date = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('Y-m-d\TH:i') : '';
@endphp

@section('title', $editing ? 'Code '.$coupon->code : 'Nouveau code promo')

@section('content')
<x-espace.header :back="route('shop-admin.coupons.index')" back-label="Codes promo" :title="$editing ? 'Code '.$coupon->code : 'Nouveau code promo'" />

<x-espace.body>
    <form method="POST" action="{{ $editing ? route('shop-admin.coupons.update', $coupon) : route('shop-admin.coupons.store') }}" class="card max-w-2xl space-y-5" x-data="{ type: @js(old('type', $coupon->type)) }">
        @csrf
        @if($editing) @method('PUT') @endif
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="code" class="field-label">Code *</label>
                <input id="code" name="code" value="{{ old('code', $coupon->code) }}" required maxlength="40" class="field-input font-mono uppercase" placeholder="BIENVENUE10">
                @error('code')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="type" class="field-label">Type de réduction</label>
                <select id="type" name="type" x-model="type" class="field-input">
                    <option value="percent">Pourcentage</option>
                    <option value="fixed">Montant fixe</option>
                </select>
            </div>
            <div>
                <label for="value" class="field-label" x-text="type === 'percent' ? 'Pourcentage *' : 'Montant (FCFA) *'">Valeur *</label>
                <input id="value" type="number" name="value" min="1" value="{{ old('value', $coupon->value) }}" required class="field-input">
                @error('value')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="min_amount" class="field-label">Montant minimum d’achat (FCFA)</label>
                <input id="min_amount" type="number" name="min_amount" min="0" value="{{ old('min_amount', $coupon->min_amount) }}" class="field-input">
            </div>
            <div><label for="starts_at" class="field-label">Début</label><input id="starts_at" type="datetime-local" name="starts_at" value="{{ old('starts_at', $date($coupon->starts_at)) }}" class="field-input"></div>
            <div><label for="ends_at" class="field-label">Fin</label><input id="ends_at" type="datetime-local" name="ends_at" value="{{ old('ends_at', $date($coupon->ends_at)) }}" class="field-input">@error('ends_at')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div>
                <label for="max_uses" class="field-label">Nombre d’utilisations maximum</label>
                <input id="max_uses" type="number" name="max_uses" min="1" value="{{ old('max_uses', $coupon->max_uses) }}" class="field-input" placeholder="Illimité">
            </div>
            <label class="flex items-center gap-2.5 self-end pb-3 text-sm"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active)) class="h-4 w-4 rounded border-line text-primary-800"> Code actif</label>
        </div>
        <div class="flex justify-end gap-2 border-t border-line pt-5">
            <a href="{{ route('shop-admin.coupons.index') }}" class="btn-outline">Annuler</a>
            <button type="submit" class="btn-primary">Enregistrer</button>
        </div>
    </form>
</x-espace.body>
@endsection
