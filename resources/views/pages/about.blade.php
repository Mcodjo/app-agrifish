@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])
@section('og_title', $seo['title'])
@section('og_description', $seo['description'])
@section('og_url', $seo['url'])

@section('content')
<section class="relative overflow-hidden bg-primary-dark text-white">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 36px 36px;"></div>
    <div class="max-w-content mx-auto px-4 lg:px-8 pt-20 pb-20 lg:pt-28 lg:pb-24 relative z-10">
        <div class="max-w-3xl">
            <p class="text-sm font-bold uppercase tracking-[0.24em] text-gold-light">À propos</p>
            <h1 class="mt-4 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-7xl">Une équipe de terrain, une culture du résultat.</h1>
            <p class="mt-6 text-lg leading-8 text-white/78 lg:text-xl">AgriFish aide les porteurs de projets, coopératives et partenaires à structurer des missions agricoles et aquacoles avec méthode.</p>
        </div>
    </div>
</section>

<section class="bg-white py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-[1fr_1fr] lg:items-start">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.24em] text-gold">Notre mission</p>
                <h2 class="mt-3 text-3xl font-extrabold text-dark lg:text-5xl">Structurer des projets utiles, lisibles et rentables.</h2>
                <p class="mt-5 text-lg leading-8 text-gray-600">Nous apportons un accompagnement clair, mobile et documenté pour aider nos clients à mieux décider et à mieux exécuter.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach([
                    ['Vision', 'Devenir une référence régionale pour l’accompagnement agricole et aquacole.'],
                    ['Mission', 'Apporter des services techniques fiables et accessibles.'],
                    ['Approche', 'Écoute, diagnostic, méthode et suivi.'],
                    ['Promesse', 'Des livrables simples, utiles et actionnables.'],
                ] as $card)
                    <div class="rounded-3xl bg-cream p-6 ring-1 ring-gray-100">
                        <div class="text-lg font-bold text-dark">{{ $card[0] }}</div>
                        <p class="mt-3 text-sm leading-7 text-gray-600">{{ $card[1] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="bg-cream py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8">
        <div class="mb-10">
            <p class="text-sm font-bold uppercase tracking-[0.24em] text-gold">Équipe</p>
            <h2 class="mt-3 text-3xl font-extrabold text-dark lg:text-5xl">Les personnes derrière AgriFish</h2>
        </div>
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach([
                ['Aminata Traoré', 'Direction générale', 'Coordination des missions et relation partenaires.'],
                ['Moussa Koné', 'Agronomie', 'Diagnostic terrain et accompagnement des exploitations.'],
                ['Awa Diallo', 'Aquaculture', 'Conception des unités piscicoles et suivi technique.'],
                ['Jean Paul N’Guessan', 'Études & données', 'Analyses, reporting et structuration des livrables.'],
            ] as $member)
                <article class="rounded-3xl bg-white p-6 text-center shadow-sm ring-1 ring-gray-100 card-hover">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-primary-dark to-primary text-2xl font-extrabold text-white">{{ mb_substr($member[0], 0, 1) }}</div>
                    <h3 class="mt-4 text-xl font-bold text-dark">{{ $member[0] }}</h3>
                    <p class="mt-1 text-sm font-semibold text-primary">{{ $member[1] }}</p>
                    <p class="mt-3 text-sm leading-7 text-gray-500">{{ $member[2] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-20 lg:py-24 text-center">
    <div class="max-w-content mx-auto px-4 lg:px-8">
        <h2 class="text-3xl font-extrabold text-dark lg:text-5xl">Travaillons ensemble</h2>
        <p class="mx-auto mt-5 max-w-2xl text-lg leading-8 text-gray-600">Que vous soyez producteur, investisseur ou coopérative, AgriFish peut vous aider à structurer votre prochaine étape.</p>
        <a href="{{ route('contact') }}" class="mt-8 inline-flex items-center justify-center rounded-xl bg-primary-dark px-7 py-4 font-bold text-white transition hover:bg-primary">Prendre contact</a>
    </div>
</section>
@endsection
