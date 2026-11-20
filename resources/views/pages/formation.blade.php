@extends('layouts.app')

@section('title', 'Formation')
@section('meta_description', 'Formations AgriFish en maraîchage, pisciculture, irrigation et gestion d’exploitation, pour les producteurs, les techniciens et les groupes.')

@php
    $formations = config('site.formations');
    $categories = array_values(array_unique(array_column($formations, 'categorie')));
@endphp

@section('content')
<section class="border-b border-line">
    <div class="max-w-content grid items-center gap-10 px-site pb-16 pt-14 sm:pt-20 lg:grid-cols-[1.1fr_1fr] lg:gap-16 lg:pb-20">
        <div>
            <p class="text-sm font-medium text-muted">Formation</p>
            <h1 class="display-lg mt-5">Des formations pratiques pour les producteurs et les techniciens</h1>
            <p class="lead mt-6 max-w-xl">Des sessions courtes, centrées sur les gestes et les décisions du quotidien. Elles s’adressent aux participants individuels comme aux coopératives, entreprises et projets qui veulent former leurs équipes.</p>
            <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                <a href="#catalogue" class="btn-primary">Voir le catalogue</a>
                <a href="{{ route('contact', ['sujet' => 'Formation']) }}" class="btn-secondary">Organiser une session de groupe</a>
            </div>
        </div>
        <div class="aspect-[4/3] overflow-hidden rounded-[20px] sm:rounded-panel">
            <x-photo name="formation-producteurs" eager sizes="(min-width: 1024px) 520px, 100vw"
                     alt="Un formateur présente des semences à un groupe de productrices, dans une parcelle de démonstration." />
        </div>
    </div>
</section>

<section class="max-w-content px-site py-16 lg:py-20">
    <dl class="grid gap-x-8 gap-y-8 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['Contenu pratique', 'Exercices et cas tirés de situations rencontrées sur le terrain.'],
            ['Supports écrits', 'Fiches techniques remises aux participants, à conserver après la session.'],
            ['Sessions de groupe', 'Dates, lieu et contenu adaptés aux besoins de votre structure.'],
            ['Attestation', 'Une attestation de formation est remise à chaque participant.'],
        ] as [$titre, $texte])
            <div class="border-t border-dark/15 pt-5">
                <dt class="font-semibold text-dark">{{ $titre }}</dt>
                <dd class="mt-2 text-[15px] leading-7 text-muted">{{ $texte }}</dd>
            </div>
        @endforeach
    </dl>
</section>

<section id="catalogue" class="max-w-content px-site pb-20 lg:pb-28" x-data="{ filtre: 'Toutes' }">
    <div class="panel px-5 py-10 sm:px-10 lg:px-14 lg:py-14">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="display-md">Catalogue</h2>
                <p class="mt-3 text-[17px] text-muted">{{ count($formations) }} formations, classées par domaine.</p>
            </div>
            <div class="-mx-1 flex gap-2 overflow-x-auto px-1" role="group" aria-label="Filtrer par domaine">
                @foreach(array_merge(['Toutes'], $categories) as $categorie)
                    <button type="button" @click="filtre = '{{ $categorie }}'" :aria-pressed="filtre === '{{ $categorie }}'"
                            :class="filtre === '{{ $categorie }}' ? 'bg-dark text-white' : 'bg-white text-dark hover:bg-sand'"
                            class="shrink-0 rounded-full px-4 py-2 text-sm font-medium transition-colors">
                        {{ $categorie }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($formations as $formation)
                <article x-show="filtre === 'Toutes' || filtre === '{{ $formation['categorie'] }}'"
                         class="flex flex-col rounded-2xl bg-white p-6 sm:p-7">
                    <p class="text-sm text-muted">{{ $formation['categorie'] }}</p>
                    <h3 class="mt-3 font-serif text-2xl leading-snug text-dark">{{ $formation['titre'] }}</h3>
                    <p class="mt-3 flex-1 text-[15px] leading-7 text-muted">{{ $formation['description'] }}</p>
                    <dl class="mt-6 grid grid-cols-3 gap-3 border-t border-line pt-4 text-sm">
                        <div><dt class="text-muted">Modules</dt><dd class="mt-0.5 font-medium text-dark">{{ $formation['modules'] }}</dd></div>
                        <div><dt class="text-muted">Durée</dt><dd class="mt-0.5 font-medium text-dark">{{ $formation['duree'] }}</dd></div>
                        <div><dt class="text-muted">Niveau</dt><dd class="mt-0.5 font-medium text-dark">{{ $formation['niveau'] }}</dd></div>
                    </dl>
                    <a href="{{ route('contact', ['sujet' => 'Formation', 'formation' => $formation['titre']]) }}" class="btn-secondary mt-6">Demander une inscription</a>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="border-t border-line">
    <div class="max-w-content px-site py-16 lg:py-24">
        <h2 class="display-md max-w-xl">Comment s’inscrire</h2>
        <x-steps class="mt-10" :items="[
            ['Choisir la formation', 'Consultez le catalogue ou décrivez-nous le besoin de votre équipe.'],
            ['Envoyer la demande', 'Par le formulaire de contact ou depuis votre espace client.'],
            ['Confirmer les modalités', 'Nous vous proposons les dates, le lieu et les conditions de la session.'],
            ['Suivre la session', 'Vous recevez les supports et une attestation de formation à la fin.'],
        ]" />
    </div>
</section>

<section class="border-t border-line">
    <div class="max-w-content grid gap-10 px-site py-16 lg:grid-cols-[1fr_1.6fr] lg:gap-16 lg:py-24">
        <h2 class="display-md">Questions sur les formations</h2>
        <x-faq :items="config('site.faq_formation')" />
    </div>
</section>
@endsection

@section('cta')
    <x-cta-final title="Former une équipe ou un groupe de producteurs ?"
                 text="Indiquez-nous le nombre de participants, le lieu et les thèmes souhaités. Nous vous proposons un programme et un devis."
                 :primary="['Organiser une session', route('contact', ['sujet' => 'Formation'])]" />
@endsection
