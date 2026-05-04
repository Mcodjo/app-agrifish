@extends('layouts.app')

@section('title', 'Accueil')
@section('meta_description', 'AgriFish — Plateforme digitale intelligente pour l\'agriculture et l\'aquaculture en Afrique. Conseil, suivi de projets et formation.')

@section('content')

{{-- Hero Section --}}
<section class="relative overflow-hidden bg-gradient-to-br from-primary-dark via-primary to-primary-light text-white">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 36px 36px;"></div>
    <div class="max-w-content mx-auto px-4 lg:px-8 pt-20 pb-20 lg:pt-28 lg:pb-24 relative z-10">
        <div class="grid gap-12 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.24em] backdrop-blur">
                    Agriculture · Aquaculture · Études techniques
                </span>
                <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-7xl">
                    Des projets <span class="text-gold-light">clairs</span>, du diagnostic à l’exécution.
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-white/78 lg:text-xl">
                    AgriFish structure vos projets agricoles et aquacoles avec une méthode simple, des livrables utiles et un suivi orienté résultats.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-xl bg-gold px-7 py-4 font-bold text-white transition hover:bg-gold-dark">Parler à un expert</a>
                    <a href="{{ route('services') }}" class="inline-flex items-center justify-center rounded-xl border border-white/30 px-7 py-4 font-semibold text-white transition hover:bg-white/10">Voir les services</a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:gap-5">
                @foreach([
                    ['120+', 'Producteurs accompagnés'],
                    ['18', 'Projets suivis'],
                    ['3', 'Pôles d’expertise'],
                    ['24h', 'Réponse moyenne'],
                ] as $stat)
                    <div class="rounded-3xl border border-white/15 bg-white/10 p-6 backdrop-blur-md shadow-xl">
                        <div class="text-3xl font-extrabold text-gold-light">{{ $stat[0] }}</div>
                        <div class="mt-2 text-sm font-medium text-white/72">{{ $stat[1] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8">
        <div class="grid gap-8 md:grid-cols-3">
            <div class="card-hover rounded-3xl border border-gray-100 bg-cream p-8">
                <div class="text-sm font-bold uppercase tracking-[0.22em] text-gold">Proposition de valeur</div>
                <h2 class="mt-3 text-2xl font-bold text-dark">Un accompagnement concret</h2>
                <p class="mt-4 text-gray-600 leading-7">Nous transformons les besoins terrain en plans d’action lisibles, adaptés au contexte et exploitables rapidement.</p>
            </div>
            <div class="card-hover rounded-3xl border border-gray-100 bg-cream p-8">
                <div class="text-sm font-bold uppercase tracking-[0.22em] text-gold">Méthode</div>
                <h2 class="mt-3 text-2xl font-bold text-dark">Diagnostic, cadrage, suivi</h2>
                <p class="mt-4 text-gray-600 leading-7">Chaque mission avance avec un cap clair, des jalons et des livrables simples à lire et à utiliser.</p>
            </div>
            <div class="card-hover rounded-3xl border border-gray-100 bg-cream p-8">
                <div class="text-sm font-bold uppercase tracking-[0.22em] text-gold">Impact</div>
                <h2 class="mt-3 text-2xl font-bold text-dark">Des résultats mesurables</h2>
                <p class="mt-4 text-gray-600 leading-7">Nos missions visent l’amélioration durable des performances techniques, économiques et opérationnelles.</p>
            </div>
        </div>
    </div>
</section>

<section class="bg-cream py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8">
        <div class="flex items-end justify-between gap-6 mb-10">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.24em] text-gold">Stats clés</p>
                <h2 class="mt-3 text-3xl font-extrabold text-dark lg:text-5xl">Une base pensée pour évoluer</h2>
            </div>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['95%', 'Taux de satisfaction'],
                ['12', 'Pays couverts'],
                ['48h', 'Temps de cadrage moyen'],
                ['100%', 'Responsive mobile'],
            ] as $stat)
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="text-3xl font-extrabold text-primary-dark">{{ $stat[0] }}</div>
                    <div class="mt-2 text-sm text-gray-500">{{ $stat[1] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8">
        <div class="mb-10 flex items-end justify-between gap-6">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.24em] text-gold">Aperçu</p>
                <h2 class="mt-3 text-3xl font-extrabold text-dark lg:text-5xl">Trois axes d’action</h2>
            </div>
            <a href="{{ route('case-studies') }}" class="hidden text-sm font-semibold text-primary hover:text-primary-dark md:inline-flex">Voir les projets</a>
        </div>
        <div class="grid gap-6 lg:grid-cols-3">
            @foreach([
                ['Agriculture', 'Conseil agricole', 'Diagnostic terrain, recommandations, suivi technique et structuration de production.'],
                ['Aquaculture', 'Développement piscicole', 'Conception, dimensionnement, pilotage eau et appui au démarrage.'],
                ['Études techniques', 'Études de faisabilité', 'Analyse technique, budget prévisionnel et scénarios de rentabilité.'],
            ] as $item)
                <article class="overflow-hidden rounded-3xl border border-gray-100 shadow-sm card-hover">
                    <div class="aspect-[4/3] bg-gradient-to-br from-primary-dark via-primary to-primary-light p-6 text-white">
                        <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em]">{{ $item[0] }}</span>
                        <h3 class="mt-16 max-w-xs text-2xl font-bold leading-8">{{ $item[1] }}</h3>
                    </div>
                    <div class="bg-white p-6">
                        <p class="text-gray-600 leading-7">{{ $item[2] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-cream py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[0.92fr_1.08fr] lg:items-center">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.24em] text-gold">Réalisations récentes</p>
                <h2 class="mt-3 text-3xl font-extrabold text-dark lg:text-5xl">Des missions documentées</h2>
                <p class="mt-5 text-lg leading-8 text-gray-600">Nos cas d’usage montrent comment nous aidons les organisations à passer du besoin flou à un plan d’action concret.</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                @foreach([
                    'Structuration d’une coopérative maraîchère',
                    'Mise en place d’une unité piscicole pilote',
                    'Rapport de faisabilité pour un site mixte',
                    'Atelier de formation terrain et suivi',
                ] as $project)
                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                        <div class="text-sm font-bold uppercase tracking-[0.2em] text-gold">Projet</div>
                        <p class="mt-3 text-lg font-bold text-dark">{{ $project }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8 text-center">
        <h2 class="text-3xl font-extrabold text-dark lg:text-5xl">Lancez votre projet avec une base solide</h2>
        <p class="mx-auto mt-5 max-w-2xl text-lg leading-8 text-gray-600">Nous pouvons vous aider à cadrer votre besoin, choisir la bonne prestation et avancer vite.</p>
        <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
            <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-dark px-7 py-4 font-bold text-white transition hover:bg-primary">Nous contacter</a>
            <a href="{{ route('services') }}" class="inline-flex items-center justify-center rounded-xl border border-primary px-7 py-4 font-semibold text-primary transition hover:bg-primary hover:text-white">Voir les services</a>
        </div>
    </div>
</section>
@endsection
