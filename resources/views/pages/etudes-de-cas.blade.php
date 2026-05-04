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
            <p class="text-sm font-bold uppercase tracking-[0.24em] text-gold-light">Réalisations</p>
            <h1 class="mt-4 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-7xl">Projets, réalisations et galerie terrain.</h1>
            <p class="mt-6 text-lg leading-8 text-white/78 lg:text-xl">Une sélection de projets et d’images pour illustrer notre approche de terrain, notre rigueur et la variété des missions réalisées.</p>
        </div>
    </div>
</section>

<section class="bg-cream py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8">
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach([
                ['Agriculture', 'Coopérative maraîchère', 'Diagnostic, planification et montée en compétence.', 'Kindia, Guinée'],
                ['Aquaculture', 'Unité piscicole pilote', 'Étude technique et lancement opérationnel.', 'Abidjan, Côte d’Ivoire'],
                ['Études', 'Rapport de faisabilité mixte', 'Scénarios techniques et financiers.', 'Bamako, Mali'],
                ['Formation', 'Atelier terrain', 'Sessions pratiques pour opérateurs et encadrants.', 'Dakar, Sénégal'],
                ['Conseil', 'Structuration de projet', 'Appui au pilotage et à la décision.', 'Accra, Ghana'],
                ['Suivi', 'Tableau de bord projet', 'Suivi des jalons et capitalisation.', 'Ouagadougou, Burkina Faso'],
            ] as $project)
                <article class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-gray-100 card-hover">
                    <div class="aspect-[4/3] bg-gradient-to-br from-primary-dark via-primary to-primary-light p-6 text-white">
                        <div class="flex items-center justify-between gap-3">
                            <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em]">{{ $project[0] }}</span>
                            <span class="text-xs font-semibold text-white/70">{{ $project[3] }}</span>
                        </div>
                        <div class="mt-16 max-w-[15rem] text-2xl font-bold leading-9">{{ $project[1] }}</div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 leading-7">{{ $project[2] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8">
        <div class="rounded-[2rem] bg-primary-dark px-6 py-10 text-white shadow-2xl sm:px-10 lg:px-14">
            <div class="grid gap-8 lg:grid-cols-[1fr_auto] lg:items-center">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.24em] text-gold-light">Votre projet</p>
                    <h2 class="mt-3 text-3xl font-extrabold lg:text-5xl">Votre prochaine réalisation peut commencer ici.</h2>
                    <p class="mt-4 max-w-2xl text-white/75 leading-8">Partagez votre besoin et nous vous proposons une approche adaptée, qu’il s’agisse d’une production, d’une étude ou d’un accompagnement technique.</p>
                </div>
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-xl bg-gold px-7 py-4 font-bold text-white transition hover:bg-gold-dark">Lancer un projet</a>
            </div>
        </div>
    </div>
</section>
@endsection
