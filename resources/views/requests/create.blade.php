@extends('layouts.app')

@section('title', 'Nouvelle demande')
@section('espace', true)

@section('content')
<x-app-header :back="route('dashboard')" back-label="Tableau de bord" eyebrow="Nouvelle demande"
              title="Décrivez votre projet"
              subtitle="Quelques minutes suffisent. Un expert AgriFish analyse votre demande et vous répond dans votre espace." />

<section class="max-w-content px-site py-10 lg:py-12">
    <div class="grid items-start gap-6 lg:grid-cols-[1fr_20rem]">
        <form action="{{ route('requests.store') }}" method="POST" enctype="multipart/form-data"
              x-data="{ sending: false, desc: @js(old('description', '')) }" @submit="sending = true"
              class="space-y-8 rounded-xl border border-line bg-white p-5 sm:p-8" novalidate>

            @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                    <p class="font-semibold">Certains champs sont à corriger.</p>
                </div>
            @endif

            {{-- Étape 1 --}}
            <fieldset>
                @csrf
                <legend class="flex items-center gap-3 text-base font-semibold text-dark">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-primary-800 text-xs text-white">1</span>
                    Type d’accompagnement
                </legend>
                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    @foreach($serviceTypes as $value => $label)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="service_type" value="{{ $value }}" class="peer sr-only" @checked(old('service_type') === $value) required>
                            <span class="flex h-full items-center gap-2 rounded-[10px] border border-line bg-white px-4 py-3 text-[15px] font-medium text-dark transition peer-checked:border-primary-800 peer-checked:bg-primary-50 peer-checked:ring-1 peer-checked:ring-primary-800 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-400 hover:border-dark/30">
                                {{ $label }}
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('service_type')<p class="field-error">Choisissez un type d’accompagnement.</p>@enderror
            </fieldset>

            {{-- Étape 2 --}}
            <fieldset class="space-y-5">
                <legend class="flex items-center gap-3 text-base font-semibold text-dark">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-primary-800 text-xs text-white">2</span>
                    Votre besoin
                </legend>
                <div>
                    <label for="title" class="field-label">Titre de la demande *</label>
                    <input id="title" name="title" type="text" required maxlength="150" value="{{ old('title') }}"
                           class="field-input @error('title') border-red-400 @enderror" placeholder="Ex : Diagnostic d’une parcelle de 5 hectares">
                    @error('title')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <div class="flex items-end justify-between">
                        <label for="description" class="field-label">Description *</label>
                        <span class="mb-1.5 text-xs" :class="desc.length < 20 ? 'text-muted' : 'text-primary-800'" x-text="desc.length + ' / 5000'"></span>
                    </div>
                    <textarea id="description" name="description" rows="7" required minlength="20" maxlength="5000" x-model="desc"
                              class="field-input @error('description') border-red-400 @enderror"
                              placeholder="Contexte (lieu, surface, espèces…), objectifs, contraintes et délais souhaités."></textarea>
                    <p class="mt-1.5 text-xs text-muted" x-show="desc.length < 20">20 caractères minimum.</p>
                    @error('description')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <span class="field-label">Priorité *</span>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['low' => 'Faible', 'normal' => 'Normale', 'high' => 'Urgente'] as $value => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="priority" value="{{ $value }}" class="peer sr-only" @checked(old('priority', 'normal') === $value)>
                                <span class="block rounded-[10px] border border-line bg-white px-3 py-2.5 text-center text-[15px] font-medium text-dark transition peer-checked:border-primary-800 peer-checked:bg-primary-50 peer-checked:ring-1 peer-checked:ring-primary-800 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-400">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('priority')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            </fieldset>

            {{-- Étape 3 --}}
            <fieldset>
                <legend class="flex items-center gap-3 text-base font-semibold text-dark">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-primary-800 text-xs text-white">3</span>
                    Documents <span class="text-sm font-normal text-muted">(facultatif)</span>
                </legend>
                <p class="mt-2 text-sm text-muted">Plans, photos, analyses de sol ou d’eau, devis… tout ce qui aide l’expert à comprendre votre projet.</p>
                <div class="mt-4">
                    <x-file-input name="documents[]" id="documents" multiple hint="Jusqu’à 10 fichiers · PDF, Word, Excel ou image · 5 Mo max" />
                </div>
                @error('documents')<p class="field-error">{{ $message }}</p>@enderror
                @error('documents.*')<p class="field-error">{{ $message }}</p>@enderror
            </fieldset>

            <div class="flex flex-col-reverse gap-3 border-t border-line pt-6 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('dashboard') }}" class="text-center text-sm text-muted hover:text-dark">Annuler</a>
                <button type="submit" class="btn-primary px-7" :disabled="sending">
                    <svg x-show="sending" x-cloak class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                    <span x-text="sending ? 'Envoi en cours…' : 'Envoyer ma demande'">Envoyer ma demande</span>
                </button>
            </div>
        </form>

        <aside class="space-y-4 lg:sticky lg:top-28">
            <div class="rounded-xl border border-line bg-white p-5">
                <h2 class="font-semibold text-dark">Et ensuite ?</h2>
                <ol class="mt-4 space-y-4 text-sm">
                    @foreach([
                        ['Analyse', 'Notre équipe étudie votre demande sous 48 h.'],
                        ['Assignation', 'Un expert du domaine est désigné pour votre projet.'],
                        ['Plan d’action', 'Les étapes et livrables apparaissent dans votre espace.'],
                    ] as $i => [$step, $text])
                        <li class="flex gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cream text-xs font-semibold text-dark">{{ $i + 1 }}</span>
                            <div><p class="font-semibold text-dark">{{ $step }}</p><p class="text-muted">{{ $text }}</p></div>
                        </li>
                    @endforeach
                </ol>
            </div>
            <div class="rounded-xl border border-line bg-white p-5 text-sm">
                <p class="text-xs text-muted">Demande au nom de</p>
                <p class="mt-2 font-semibold text-dark">{{ $user->name }}</p>
                <p class="text-muted">{{ $user->email }}</p>
                <p class="text-muted">{{ $user->phone ?? 'Téléphone non renseigné' }}</p>
                <a href="{{ route('profile.edit') }}" class="mt-3 inline-block font-medium text-primary-800 hover:underline">Modifier mes coordonnées</a>
            </div>
        </aside>
    </div>
</section>
@endsection
