@extends('layouts.espace')

@section('title', 'Mes adresses')

@php
    $fields = [
        ['label', 'Nom de l’adresse', false, 'Ex : Ferme, Bureau'],
        ['first_name', 'Prénom', true, ''],
        ['last_name', 'Nom', true, ''],
        ['phone', 'Téléphone', true, ''],
        ['city', 'Ville', true, ''],
        ['district', 'Quartier', false, ''],
        ['details', 'Adresse ou repère', false, ''],
    ];
@endphp

@section('content')
<x-espace.header title="Mes adresses" subtitle="Vos coordonnées enregistrées sont proposées au moment de commander." />

<x-espace.body>
    <div class="grid items-start gap-6 lg:grid-cols-[1fr_22rem]">
        <div class="space-y-4">
            @forelse($addresses as $address)
                <div class="rounded-xl border border-line bg-white p-5" x-data="{ edit: false }">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="flex items-center gap-2 font-semibold text-dark">
                                {{ $address->label ?: $address->city }}
                                @if($address->is_default)<span class="rounded-full bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-800">Par défaut</span>@endif
                            </p>
                            <p class="mt-1 text-sm leading-6 text-muted">{{ $address->first_name }} {{ $address->last_name }} · {{ $address->phone }}<br>{{ $address->oneLine() }}</p>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <button type="button" @click="edit = !edit" class="font-medium text-primary-800" x-text="edit ? 'Fermer' : 'Modifier'">Modifier</button>
                            <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" onsubmit="return confirm('Supprimer cette adresse ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-700 hover:underline">Supprimer</button>
                            </form>
                        </div>
                    </div>
                    <form x-show="edit" x-cloak method="POST" action="{{ route('account.addresses.update', $address) }}" class="mt-4 grid gap-3 rounded-lg bg-cream p-4 sm:grid-cols-2">
                        @csrf @method('PATCH')
                        @foreach($fields as [$name, $label, $required, $placeholder])
                            <div class="{{ $name === 'details' ? 'sm:col-span-2' : '' }}">
                                <label class="field-label" for="{{ $name }}-{{ $address->id }}">{{ $label }}</label>
                                <input id="{{ $name }}-{{ $address->id }}" name="{{ $name }}" value="{{ $address->{$name} }}" @if($required) required @endif placeholder="{{ $placeholder }}" class="field-input">
                            </div>
                        @endforeach
                        <label class="flex items-center gap-2 text-sm sm:col-span-2"><input type="checkbox" name="is_default" value="1" @checked($address->is_default) class="h-4 w-4 rounded border-line text-primary-800"> Adresse par défaut</label>
                        <div class="sm:col-span-2"><button type="submit" class="btn-primary">Enregistrer</button></div>
                    </form>
                </div>
            @empty
                <x-espace.empty title="Aucune adresse enregistrée" text="Ajoutez une adresse pour gagner du temps lors de vos prochaines commandes." />
            @endforelse
        </div>

        <x-espace.section title="Ajouter une adresse">
            <form method="POST" action="{{ route('account.addresses.store') }}" class="space-y-3">
                @csrf
                @foreach($fields as [$name, $label, $required, $placeholder])
                    <div>
                        <label class="field-label" for="new-{{ $name }}">{{ $label }}{{ $required ? ' *' : '' }}</label>
                        <input id="new-{{ $name }}" name="{{ $name }}" value="{{ old($name) }}" @if($required) required @endif placeholder="{{ $placeholder }}" class="field-input @error($name) border-red-400 @enderror">
                        @error($name)<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                @endforeach
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_default" value="1" class="h-4 w-4 rounded border-line text-primary-800"> Adresse par défaut</label>
                <button type="submit" class="btn-primary w-full">Ajouter</button>
            </form>
        </x-espace.section>
    </div>
</x-espace.body>
@endsection
