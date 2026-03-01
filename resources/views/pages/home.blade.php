@extends('layouts.app')

@section('title', 'Accueil')
@section('meta_description', 'AgriFish — Plateforme digitale intelligente pour l\'agriculture et l\'aquaculture en Afrique. Conseil, suivi de projets et formation.')

@section('content')

{{-- ===================================================
     HERO
===================================================== --}}
<section class="hero-gradient min-h-screen flex items-center relative overflow-hidden">
    {{-- Motif décoratif --}}
    <div class="absolute inset-0 opacity-10">
        <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 200" fill="none">
            <path d="M0,160 C360,220 1080,80 1440,120 L1440,200 L0,200 Z" fill="white"/>
        </svg>
    </div>
    <div class="absolute top-20 right-0 w-96 h-96 bg-primary-light/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-gold/10 rounded-full blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-20 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 text-gold-light text-sm font-medium rounded-full mb-6 border border-gold/30">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                    Digitalisation agricole en Afrique
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                    L'agriculture africaine entre dans l'ère <span class="text-gold-light">numérique</span>
                </h1>
                <p class="text-lg text-white/80 leading-relaxed mb-8 max-w-xl">
                    AgriFish centralise le conseil agricole, la gestion de projets, les services techniques et la formation digitale au sein d'un seul écosystème intelligent.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('services') }}"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-gold text-white font-semibold rounded-xl shadow-lg hover:bg-gold-dark">
                        Découvrir nos services
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="{{ route('contact') }}"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-white/10 border border-white/30 text-white font-semibold rounded-xl hover:bg-white/20 backdrop-blur-sm">
                        Nous contacter
                    </a>
                </div>
            </div>

            {{-- Cartes flottantes --}}
            <div class="hidden lg:flex flex-col gap-4 relative">
                <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6 transform hover:-translate-y-1 transition-transform">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary-light/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary-100" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                        </div>
                        <div>
                            <p class="text-white font-semibold">Suivi projet en temps réel</p>
                            <p class="text-white/60 text-sm">Tableau de bord personnalisé</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6 ml-8 transform hover:-translate-y-1 transition-transform">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gold/20 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gold-light" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <p class="text-white font-semibold">Formations certifiantes</p>
                            <p class="text-white/60 text-sm">Agriculture & pisciculture</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6 transform hover:-translate-y-1 transition-transform">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-white font-semibold">Experts disponibles</p>
                            <p class="text-white/60 text-sm">Réponse sous 24h</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===================================================
     CHIFFRES CLÉS
===================================================== --}}
<section class="bg-primary py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            @foreach([
                ['number' => '150+', 'label' => 'Projets accompagnés'],
                ['number' => '12',   'label' => 'Pays couverts'],
                ['number' => '50+',  'label' => 'Experts certifiés'],
                ['number' => '2000+','label' => 'Producteurs formés'],
            ] as $stat)
                <div>
                    <p class="text-4xl font-extrabold text-gold-light">{{ $stat['number'] }}</p>
                    <p class="text-white/80 text-sm mt-1">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================================================
     NOS SERVICES
===================================================== --}}
<section class="py-24 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider">Ce que nous faisons</span>
            <h2 class="text-3xl sm:text-4xl font-bold text-dark mt-2 mb-4">Nos pôles d'expertise</h2>
            <p class="text-gray-500 max-w-2xl mx-auto">
                Une offre complète pour digitaliser, structurer et accélérer la croissance de votre activité agricole ou piscicole.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach([
                [
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>',
                    'title' => 'Conseil agricole',
                    'desc'  => 'Expertise terrain, études de sol, plans de culture et accompagnement personnalisé pour optimiser vos rendements.',
                    'color' => 'bg-primary-50 text-primary',
                ],
                [
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>',
                    'title' => 'Gestion de projets',
                    'desc'  => 'Suivi en temps réel, documents partagés, rapports automatiques et communication directe avec vos experts.',
                    'color' => 'bg-gold/10 text-gold-dark',
                ],
                [
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                    'title' => 'Formation digitale',
                    'desc'  => 'Modules vidéo certifiants sur l\'agriculture moderne, la pisciculture et la gestion d\'exploitation agricole.',
                    'color' => 'bg-blue-50 text-blue-700',
                ],
            ] as $service)
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 card-hover">
                    <div class="w-14 h-14 rounded-xl {{ $service['color'] }} flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $service['icon'] !!}</svg>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-3">{{ $service['title'] }}</h3>
                    <p class="text-gray-500 leading-relaxed mb-5">{{ $service['desc'] }}</p>
                    <a href="{{ route('services') }}" class="inline-flex items-center text-primary font-semibold text-sm hover:text-primary-dark gap-1">
                        En savoir plus <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================================================
     POURQUOI AGRIFISH
===================================================== --}}
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="text-primary font-semibold text-sm uppercase tracking-wider">Notre différence</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-dark mt-2 mb-6">
                    Pensée pour l'Afrique, <br>conçue pour la <span class="text-primary">performance</span>
                </h2>
                <p class="text-gray-500 leading-relaxed mb-8">
                    AgriFish est la seule plateforme qui comprend les réalités du terrain africain tout en offrant des outils numériques de niveau international.
                </p>
                <div class="space-y-5">
                    @foreach([
                        ['Optimisée pour les connexions faibles', 'Accessible dans les zones rurales sans internet haut débit.'],
                        ['Mobile-first', 'Conçue avant tout pour les smartphones, l\'outil du producteur africain.'],
                        ['Paiements locaux intégrés', 'Mobile Money, Orange Money, Wave et autres solutions locales.'],
                        ['Interface simple et intuitive', 'Prise en main en quelques minutes, même sans formation digitale.'],
                    ] as $point)
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-primary-50 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-dark">{{ $point[0] }}</p>
                                <p class="text-gray-500 text-sm mt-0.5">{{ $point[1] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-5">
                @foreach([
                    ['🌱', 'Agriculture', '+ de 80 types de cultures accompagnées'],
                    ['🐟', 'Aquaculture', 'Pisciculture, crevetticulture, maricuture'],
                    ['📊', 'Rapports', 'Rapports techniques automatisés'],
                    ['🎓', 'Certifications', 'Certificats reconnus téléchargeables'],
                ] as $card)
                    <div class="bg-cream rounded-2xl p-6 border border-gray-100">
                        <p class="text-3xl mb-3">{{ $card[0] }}</p>
                        <p class="font-bold text-dark">{{ $card[1] }}</p>
                        <p class="text-gray-400 text-sm mt-1">{{ $card[2] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ===================================================
     CTA FINAL
===================================================== --}}
<section class="py-24 bg-primary-dark">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-5">
            Prêt à digitaliser votre activité agricole ?
        </h2>
        <p class="text-white/70 text-lg mb-10 max-w-2xl mx-auto">
            Rejoignez des centaines de producteurs, coopératives et investisseurs qui font confiance à AgriFish pour développer leurs projets.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact') }}"
               class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gold text-white font-bold rounded-xl text-lg shadow-xl hover:bg-gold-dark">
                Démarrer maintenant
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            <a href="{{ route('formation') }}"
               class="inline-flex items-center justify-center gap-2 px-8 py-4 border-2 border-white/40 text-white font-semibold rounded-xl text-lg hover:bg-white/10">
                Voir les formations
            </a>
        </div>
    </div>
</section>

@endsection
