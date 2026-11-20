@extends('layouts.app')

@section('title', 'Réalisations')
@section('meta_description', 'Exemples de missions AgriFish : conseil agricole, projets piscicoles, études de faisabilité, formation et suivi de projets en Afrique de l’Ouest.')

@php
    $projets = config('site.realisations');
    $secteurs = array_values(array_unique(array_column($projets, 'secteur')));
@endphp

@section('content')
<x-page-header label="Réalisations"
               title="Des missions menées sur le terrain"
               intro="Coopératives, unités piscicoles, études préalables et formations : quelques exemples de projets accompagnés par AgriFish." />

<section class="max-w-content px-site py-16 lg:py-20" x-data="{ filtre: 'Tous' }">
    <div class="-mx-1 flex gap-2 overflow-x-auto px-1" role="group" aria-label="Filtrer par secteur">
        @foreach(array_merge(['Tous'], $secteurs) as $secteur)
            <button type="button" @click="filtre = '{{ $secteur }}'" :aria-pressed="filtre === '{{ $secteur }}'"
                    :class="filtre === '{{ $secteur }}' ? 'bg-dark text-white' : 'bg-cream text-dark hover:bg-sand'"
                    class="shrink-0 rounded-full px-4 py-2 text-sm font-medium transition-colors">
                {{ $secteur }}
            </button>
        @endforeach
    </div>

    <div class="mt-8 divide-y divide-line border-y border-line">
        @foreach($projets as $projet)
            <article x-show="filtre === 'Tous' || filtre === '{{ $projet['secteur'] }}'"
                     class="grid gap-3 py-8 sm:grid-cols-[10rem_1fr] lg:grid-cols-[12rem_1.2fr_1fr_12rem] lg:items-baseline lg:gap-8">
                <p class="text-sm text-muted">{{ $projet['secteur'] }}</p>
                <h2 class="font-serif text-2xl leading-snug text-dark sm:col-start-2 lg:col-start-auto">{{ $projet['titre'] }}</h2>
                <p class="text-[15px] leading-7 text-muted sm:col-start-2 lg:col-start-auto">{{ $projet['description'] }}</p>
                <p class="flex items-center gap-1.5 text-sm text-dark sm:col-start-2 lg:col-start-auto lg:justify-end">
                    <svg class="h-4 w-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-6.2-7-11.5a7 7 0 1 1 14 0C19 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
                    {{ $projet['lieu'] }}
                </p>
            </article>
        @endforeach
    </div>
</section>

<section class="max-w-content px-site pb-20 lg:pb-28">
    <div class="panel grid items-center gap-8 px-6 py-10 sm:px-10 lg:grid-cols-[1.2fr_1fr] lg:px-14 lg:py-14">
        <div>
            <h2 class="display-md">Ce que nous livrons</h2>
            <p class="mt-4 max-w-xl text-[17px] leading-8 text-muted">Chaque mission donne lieu à des documents remis au client et conservés dans son espace en ligne.</p>
        </div>
        <ul class="grid gap-3 text-[15px] text-dark sm:grid-cols-2">
            @foreach(['Rapport de diagnostic', 'Plan d’action chiffré', 'Fiches de suivi technique', 'Étude de faisabilité', 'Supports de formation', 'Bilan de fin de mission'] as $livrable)
                <li class="rounded-xl bg-white px-4 py-3">{{ $livrable }}</li>
            @endforeach
        </ul>
    </div>
</section>
@endsection

@section('cta')
    <x-cta-final title="Votre projet pourrait être le prochain"
                 text="Production végétale, élevage piscicole, étude préalable ou formation : présentez-nous votre projet."
                 :primary="['Présenter mon projet', route('contact')]" />
@endsection
