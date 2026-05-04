@extends('layouts.app')

@section('title', 'Nos services')
@section('meta_description', 'Conseil agricole, aquaculture et études techniques pour vos projets en Afrique.')
@section('og_title', 'Nos services')
@section('og_description', 'Conseil agricole, aquaculture et études techniques pour vos projets en Afrique.')
@section('og_url', url()->current())

@section('content')
<section class="relative overflow-hidden bg-primary-dark text-white">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 36px 36px;"></div>
    <div class="max-w-content mx-auto px-4 lg:px-8 pt-20 pb-20 lg:pt-28 lg:pb-24 relative z-10 text-center">
        <p class="text-sm font-bold uppercase tracking-[0.24em] text-gold-light">Nos services</p>
        <h1 class="mt-4 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-7xl">Agriculture, aquaculture et études techniques.</h1>
        <p class="mx-auto mt-6 max-w-3xl text-lg leading-8 text-white/78 lg:text-xl">Des prestations claires pour concevoir, sécuriser et améliorer vos projets avec une logique terrain et des livrables actionnables.</p>
    </div>
</section>

<section class="bg-white py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-3">
            @foreach([
                ['Agriculture', 'Accompagnement agricole', 'Diagnostic de parcelle, conseils agronomiques, itinéraires techniques et suivi des cultures.', ['Diagnostic terrain', 'Plan cultural', 'Suivi technique', 'Recommandations']],
                ['Aquaculture', 'Développement piscicole', 'Conception de sites d’élevage, assistance technique, gestion de l’eau et performance des systèmes.', ['Choix du site', 'Dimensionnement', 'Pilotage eau', 'Optimisation rendement']],
                ['Études techniques', 'Études de faisabilité', 'Pré-études, chiffrage, scénarios d’investissement et analyse de rentabilité.', ['Faisabilité', 'Budget prévisionnel', 'Risques', 'Feuille de route']],
            ] as $service)
                <article class="rounded-3xl border border-gray-100 bg-cream p-8 shadow-sm card-hover">
                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-gold">{{ $service[0] }}</p>
                    <h2 class="mt-3 text-2xl font-bold text-dark">{{ $service[1] }}</h2>
                    <p class="mt-4 text-gray-600 leading-7">{{ $service[2] }}</p>
                    <ul class="mt-6 space-y-3">
                        @foreach($service[3] as $item)
                            <li class="flex items-center gap-3 text-sm font-medium text-dark"><span class="h-2 w-2 rounded-full bg-gold"></span>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-cream py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.24em] text-gold">Notre méthode</p>
                <h2 class="mt-3 text-3xl font-extrabold text-dark lg:text-5xl">Simple, structurée, utile.</h2>
                <p class="mt-5 text-lg leading-8 text-gray-600">Nous partons du besoin, clarifions les options, puis livrons un plan d’action adapté à votre contexte et à votre budget.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach([
                    ['01', 'Prise de contact', 'Compréhension rapide du besoin.'],
                    ['02', 'Diagnostic', 'Analyse terrain et contraintes.'],
                    ['03', 'Préconisations', 'Livrable et feuille de route.'],
                    ['04', 'Suivi', 'Ajustements et accompagnement.'],
                ] as $step)
                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                        <div class="text-sm font-bold uppercase tracking-[0.2em] text-gold">{{ $step[0] }}</div>
                        <div class="mt-3 text-xl font-bold text-dark">{{ $step[1] }}</div>
                        <p class="mt-3 text-sm leading-7 text-gray-500">{{ $step[2] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-20 lg:py-24 text-center">
    <div class="max-w-content mx-auto px-4 lg:px-8">
        <h2 class="text-3xl font-extrabold text-dark lg:text-5xl">Besoin d’un cadrage rapide ?</h2>
        <p class="mx-auto mt-5 max-w-2xl text-lg leading-8 text-gray-600">Décrivez votre projet et nous vous orienterons vers la bonne prestation.</p>
        <a href="{{ route('contact') }}" class="mt-8 inline-flex items-center justify-center rounded-xl bg-primary-dark px-7 py-4 font-bold text-white transition hover:bg-primary">Contacter AgriFish</a>
    </div>
</section>
@endsection