@extends('layouts.app')

@section('title', 'Accueil')
@section('meta_description', 'AgriFish accompagne les projets agricoles et aquacoles en Afrique de l’Ouest : conseil technique, études de faisabilité, formation et suivi de projet en ligne.')

@php
    $services = config('site.services');
    $contact = config('site.contact');
@endphp

@section('content')

{{-- Accroche --}}
<section class="max-w-content px-site pb-16 pt-10 sm:pt-14 lg:pb-24 lg:pt-16">
    <div class="grid items-center gap-10 lg:grid-cols-[1.2fr_1fr] lg:gap-14">
        <div>
            <p class="inline-flex items-center gap-2 rounded-full bg-cream py-1.5 pl-2.5 pr-3.5 text-sm text-dark">
                <span class="h-2 w-2 rounded-full bg-primary-500"></span>
                Réponse sous {{ $contact['delai'] }}
            </p>
            <h1 class="display-xl mt-6">Vos projets agricoles et aquacoles, du diagnostic au suivi.</h1>
            <p class="lead mt-6 max-w-xl">AgriFish accompagne producteurs, coopératives et investisseurs en {{ $contact['zone'] }} : conseil technique, études de faisabilité, formation, et un espace en ligne pour suivre chaque mission.</p>
            <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('contact') }}" class="btn-primary">Parler de mon projet</a>
                <a href="{{ route('services') }}" class="btn-secondary">Découvrir nos services</a>
            </div>
        </div>
        <div class="aspect-[5/4] overflow-hidden rounded-[20px] bg-cream sm:rounded-panel">
            <x-photo name="conseil-terrain" eager sizes="(min-width: 1024px) 560px, 100vw"
                     alt="Une conseillère agricole montre des données sur son téléphone à un producteur, dans un champ préparé." />
        </div>
    </div>
</section>

{{-- Chiffres clés --}}
<section class="border-y border-line">
    <dl class="max-w-content grid grid-cols-2 px-site lg:grid-cols-4">
        @foreach(config('site.chiffres') as $i => $chiffre)
            <div class="flex flex-col-reverse gap-1 py-7 lg:py-9 {{ $i % 2 ? 'pl-6 border-l border-line' : '' }} {{ $i >= 2 ? 'border-t border-line lg:border-t-0' : '' }} {{ $i === 2 ? 'lg:border-l lg:pl-6' : '' }}">
                <dt class="text-[15px] text-muted">{{ $chiffre['libelle'] }}</dt>
                <dd class="font-serif text-4xl text-dark sm:text-[2.75rem]">{{ $chiffre['valeur'] }}</dd>
            </div>
        @endforeach
    </dl>
</section>

{{-- Domaines d'intervention --}}
<section class="max-w-content px-site py-20 lg:py-28" x-data="{ tab: 'agriculture' }">
    <div class="grid gap-6 lg:grid-cols-2 lg:items-end">
        <h2 class="display-lg max-w-lg">Nos domaines d’intervention</h2>
        <p class="lead max-w-lg">Un même interlocuteur pour la production végétale, l’élevage piscicole et les études qui précèdent un investissement.</p>
    </div>

    <div class="-mx-5 mt-10 overflow-x-auto px-5 sm:mx-0 sm:px-0" role="tablist" aria-label="Domaines">
        <div class="flex gap-2">
            @foreach($services as $key => $service)
                <button type="button" role="tab" id="onglet-{{ $key }}" aria-controls="panneau-{{ $key }}"
                        :aria-selected="tab === '{{ $key }}'" @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'bg-dark text-white' : 'bg-cream text-dark hover:bg-sand'"
                        class="shrink-0 rounded-full px-5 py-2.5 text-[15px] font-medium transition-colors">
                    {{ $service['titre'] }}
                </button>
            @endforeach
        </div>
    </div>

    @foreach($services as $key => $service)
        <div id="panneau-{{ $key }}" role="tabpanel" aria-labelledby="onglet-{{ $key }}"
             x-show="tab === '{{ $key }}'" @if(! $loop->first) x-cloak @endif
             class="panel mt-4 grid gap-8 p-6 sm:p-10 lg:grid-cols-[1fr_1.05fr] lg:items-center lg:gap-14 lg:p-14">
            <div>
                <p class="text-sm text-muted">{{ $service['intitule'] }}</p>
                <h3 class="display-md mt-2">{{ $service['titre'] }}</h3>
                <p class="mt-4 text-[17px] leading-8 text-muted">{{ $service['resume'] }}</p>
                <ul class="mt-7 space-y-4">
                    @foreach($service['prestations'] as [$prestation, $detail])
                        <li class="flex gap-3">
                            <svg class="mt-1 h-5 w-5 shrink-0 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12.5 4.5 4.5L19 7.5"/></svg>
                            <span class="text-[15px] leading-6"><span class="font-semibold text-dark">{{ $prestation }}.</span> <span class="text-muted">{{ $detail }}</span></span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('services') }}#{{ $key }}" class="link-arrow mt-8">
                    Voir le détail
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-5-5 5 5-5 5"/></svg>
                </a>
            </div>
            <div class="aspect-[4/3] overflow-hidden rounded-2xl">
                <x-photo :name="$service['photo']" :alt="$service['photo_alt']" sizes="(min-width: 1024px) 540px, 100vw" />
            </div>
        </div>
    @endforeach
</section>

{{-- Méthode --}}
<section class="mx-2 rounded-[20px] bg-primary-800 text-white sm:mx-3 sm:rounded-panel">
    <div class="max-w-content px-site py-20 lg:py-24">
        <div class="grid gap-6 lg:grid-cols-2 lg:items-end">
            <h2 class="max-w-lg font-serif text-[2.2rem] leading-tight tracking-[-0.015em] sm:text-5xl">Comment se déroule une mission</h2>
            <p class="max-w-lg text-lg leading-8 text-white/70">Quatre étapes, avec un expert attitré du premier échange jusqu’au suivi.</p>
        </div>
        <x-steps :items="config('site.methode')" inverse class="mt-14" />
    </div>
</section>

{{-- Espace client --}}
<section class="max-w-content px-site py-20 lg:py-28">
    <div class="panel grid overflow-hidden lg:grid-cols-[1fr_1.1fr]">
        <div class="p-6 sm:p-10 lg:p-14">
            <p class="text-sm text-muted">Espace client</p>
            <h2 class="display-md mt-2">Suivez votre projet en ligne</h2>
            <p class="mt-4 text-[17px] leading-8 text-muted">Chaque client dispose d’un accès personnel. Il y suit l’avancement de la mission et échange directement avec l’expert chargé de son projet.</p>
            <ul class="mt-7 space-y-3 text-[15px] text-dark">
                @foreach([
                    'Étapes du projet et taux d’avancement',
                    'Documents, rapports et livrables à télécharger',
                    'Messagerie avec votre expert',
                    'E-mail à chaque changement de statut',
                ] as $point)
                    <li class="flex gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12.5 4.5 4.5L19 7.5"/></svg>
                        {{ $point }}
                    </li>
                @endforeach
            </ul>
            <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('register') }}" class="btn-primary">Créer mon espace</a>
                <a href="{{ route('login') }}" class="btn-secondary">Se connecter</a>
            </div>
        </div>
        <div class="relative min-h-[300px] sm:min-h-[420px]">
            <img src="{{ asset('images/plateforme/suivi-projet.webp') }}" width="1800" height="1286" loading="lazy" decoding="async"
                 alt="Aperçu de l’espace client : étapes du projet, avancement et messagerie avec l’expert."
                 class="absolute left-6 top-8 w-[920px] max-w-none rounded-tl-xl border border-line shadow-[0_20px_50px_-20px_rgba(16,35,26,0.35)] sm:left-10 sm:top-12 lg:top-14">
        </div>
    </div>
</section>

{{-- Réalisations --}}
<section class="max-w-content px-site pb-20 lg:pb-28">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <h2 class="display-lg">Exemples de missions</h2>
        <a href="{{ route('case-studies') }}" class="link-arrow">
            Toutes les réalisations
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-5-5 5 5-5 5"/></svg>
        </a>
    </div>
    <div class="mt-10 grid gap-4 md:grid-cols-3">
        @foreach(array_slice(config('site.realisations'), 0, 3) as $projet)
            <article class="flex flex-col rounded-2xl border border-line p-6 sm:p-7">
                <span class="tag self-start">{{ $projet['secteur'] }}</span>
                <h3 class="mt-6 font-serif text-2xl leading-snug text-dark">{{ $projet['titre'] }}</h3>
                <p class="mt-3 flex-1 text-[15px] leading-7 text-muted">{{ $projet['description'] }}</p>
                <p class="mt-6 flex items-center gap-1.5 border-t border-line pt-4 text-sm text-muted">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-6.2-7-11.5a7 7 0 1 1 14 0C19 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
                    {{ $projet['lieu'] }}
                </p>
            </article>
        @endforeach
    </div>
</section>

{{-- Formation --}}
<section class="max-w-content px-site pb-20 lg:pb-28">
    <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
        <div class="aspect-[4/3] overflow-hidden rounded-[20px] sm:rounded-panel">
            <x-photo name="formation-producteurs" sizes="(min-width: 1024px) 560px, 100vw"
                     alt="Un formateur présente des semences à un groupe de productrices, dans une parcelle de démonstration." />
        </div>
        <div>
            <p class="text-sm text-muted">Formation</p>
            <h2 class="display-md mt-2">Des formations pratiques pour les producteurs et les techniciens</h2>
            <p class="mt-4 text-[17px] leading-8 text-muted">Maraîchage, pisciculture, irrigation, gestion : des sessions courtes, organisées pour des participants individuels ou pour des groupes.</p>
            <ul class="mt-7 divide-y divide-line border-y border-line">
                @foreach(array_slice(config('site.formations'), 0, 3) as $formation)
                    <li class="flex items-baseline justify-between gap-6 py-4">
                        <span class="font-medium text-dark">{{ $formation['titre'] }}</span>
                        <span class="shrink-0 text-sm text-muted">{{ $formation['duree'] }}</span>
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('formation') }}" class="link-arrow mt-7">
                Voir le catalogue
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-5-5 5 5-5 5"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- Questions fréquentes --}}
<section class="border-t border-line">
    <div class="max-w-content grid gap-10 px-site py-20 lg:grid-cols-[1fr_1.6fr] lg:gap-16 lg:py-28">
        <div>
            <h2 class="display-lg">Questions fréquentes</h2>
            <p class="mt-4 text-[17px] leading-8 text-muted">Une autre question ? <a href="{{ route('contact') }}" class="font-semibold text-dark underline underline-offset-4">Écrivez-nous</a>.</p>
        </div>
        <x-faq :items="config('site.faq')" />
    </div>
</section>
@endsection

@section('cta')
    <x-cta-final :secondary="['Voir nos services', route('services')]" />
@endsection
