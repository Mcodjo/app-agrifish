@extends('layouts.app')

@section('title', 'À propos')
@section('meta_description', 'AgriFish est un cabinet de conseil technique agricole et aquacole qui accompagne producteurs, coopératives et investisseurs en Afrique de l’Ouest.')

@php
    $pays = collect(config('site.realisations'))->pluck('lieu')->map(fn ($lieu) => trim(explode(',', $lieu)[1] ?? $lieu))->unique()->sort()->values();
@endphp

@section('content')
<section class="border-b border-line">
    <div class="max-w-content grid items-center gap-10 px-site pb-16 pt-14 sm:pt-20 lg:grid-cols-[1.1fr_1fr] lg:gap-16 lg:pb-20">
        <div>
            <p class="text-sm font-medium text-muted">À propos</p>
            <h1 class="display-lg mt-5">Une équipe de terrain, attachée aux résultats</h1>
            <p class="lead mt-6 max-w-xl">AgriFish aide les porteurs de projets, les coopératives et leurs partenaires à préparer et conduire des projets agricoles et aquacoles avec méthode.</p>
        </div>
        <div class="aspect-[4/3] overflow-hidden rounded-[20px] sm:rounded-panel">
            <x-photo name="producteurs-tomates" eager sizes="(min-width: 1024px) 520px, 100vw"
                     alt="Deux producteurs inspectent des plants de tomates sur une parcelle irriguée." />
        </div>
    </div>
</section>

<section class="max-w-content px-site py-16 lg:py-24">
    <div class="grid gap-10 lg:grid-cols-[1fr_1.4fr] lg:gap-16">
        <h2 class="display-md">Notre mission</h2>
        <div class="space-y-5 text-[17px] leading-8 text-muted">
            <p>Nous voulons que chaque projet agricole ou aquacole parte sur des bases solides : un site bien choisi, des choix techniques adaptés, un budget réaliste et un suivi régulier.</p>
            <p>Pour cela, nous combinons le travail de terrain avec des livrables écrits clairs, et un espace en ligne où nos clients retrouvent l’avancement, les documents et les échanges avec leur expert.</p>
        </div>
    </div>

    <dl class="mt-14 grid gap-x-8 gap-y-8 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['Vision', 'Devenir une référence régionale pour l’accompagnement agricole et aquacole.'],
            ['Mission', 'Apporter des services techniques fiables et accessibles.'],
            ['Approche', 'Écouter, diagnostiquer, proposer, puis suivre la mise en œuvre.'],
            ['Engagement', 'Des livrables simples, utiles et directement exploitables.'],
        ] as [$titre, $texte])
            <div class="border-t border-dark/15 pt-5">
                <dt class="font-semibold text-dark">{{ $titre }}</dt>
                <dd class="mt-2 text-[15px] leading-7 text-muted">{{ $texte }}</dd>
            </div>
        @endforeach
    </dl>
</section>

<section class="max-w-content px-site pb-16 lg:pb-24">
    <div class="panel px-6 py-12 sm:px-10 lg:px-14 lg:py-16">
        <div class="grid gap-6 lg:grid-cols-2 lg:items-end">
            <h2 class="display-md max-w-md">Une équipe pluridisciplinaire</h2>
            <p class="max-w-lg text-[17px] leading-8 text-muted">Selon la mission, nous mobilisons les compétences suivantes, avec un expert attitré pour chaque projet.</p>
        </div>
        <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['Coordination', 'Planification des missions, suivi de l’avancement et relation avec les partenaires.'],
                ['Agronomie', 'Diagnostic de terrain, itinéraires techniques et accompagnement des exploitations.'],
                ['Aquaculture', 'Conception des unités piscicoles, gestion de l’eau et suivi technique.'],
                ['Études et données', 'Analyses, chiffrage, suivi des indicateurs et rédaction des livrables.'],
            ] as [$titre, $texte])
                <div class="rounded-2xl bg-white p-6">
                    <h3 class="font-serif text-xl text-dark">{{ $titre }}</h3>
                    <p class="mt-3 text-[15px] leading-7 text-muted">{{ $texte }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="border-t border-line">
    <div class="max-w-content grid gap-10 px-site py-16 lg:grid-cols-[1fr_1.4fr] lg:gap-16 lg:py-24">
        <div>
            <h2 class="display-md">Où intervenons-nous ?</h2>
            <p class="mt-4 text-[17px] leading-8 text-muted">Principalement en {{ config('site.contact.zone') }}. Nos missions nous ont conduits notamment dans les pays suivants.</p>
        </div>
        <ul class="grid content-start gap-x-8 sm:grid-cols-2">
            @foreach($pays as $nom)
                <li class="flex items-center justify-between border-b border-line py-4 text-[17px] text-dark">
                    {{ $nom }}
                    <svg class="h-4 w-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-6.2-7-11.5a7 7 0 1 1 14 0C19 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endsection

@section('cta')
    <x-cta-final title="Travaillons ensemble"
                 text="Producteur, coopérative, entreprise ou ONG : parlons de la prochaine étape de votre projet." />
@endsection
