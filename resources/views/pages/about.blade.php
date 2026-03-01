@extends('layouts.app')

@section('title', 'À propos')
@section('meta_description', 'Notre mission : digitaliser l\'accompagnement agricole en Afrique. Découvrez l\'équipe, les valeurs et l\'histoire d\'AgriFish.')

@section('content')

{{-- Hero --}}
<x-hero-inner
    badge="Notre histoire"
    title="À propos d'<span class='text-gold-light'>AgriFish</span>"
    subtitle="Une vision née du terrain, portée par la technologie, au service de l'agriculture africaine."
/>

{{-- Mission & Vision --}}
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="text-primary font-semibold text-sm uppercase tracking-wider">Notre raison d'être</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-dark mt-2 mb-6">
                    Transformer l'agriculture africaine par le <span class="text-primary">numérique</span>
                </h2>
                <p class="text-gray-500 leading-relaxed mb-5">
                    AgriFish est née d'un constat simple : les producteurs, coopératives et investisseurs agricoles africains manquent d'outils digitaux adaptés à leurs réalités terrain. Les solutions existantes sont soit trop complexes, soit inadaptées aux infrastructures locales.
                </p>
                <p class="text-gray-500 leading-relaxed mb-8">
                    Notre mission est de combler ce fossé en proposant une plateforme professionnelle, accessible et efficace, qui structure, accompagne et accélère le développement de l'agriculture et de l'aquaculture en Afrique.
                </p>
                <div class="grid sm:grid-cols-2 gap-5">
                    @foreach([
                        ['🎯', 'Notre mission', 'Digitaliser l\'accompagnement agricole et professionnaliser les services ruraux.'],
                        ['🔭', 'Notre vision', 'Devenir la plateforme de référence pour l\'agriculture moderne en Afrique.'],
                    ] as $mv)
                        <div class="bg-cream rounded-xl p-5 border border-gray-100">
                            <p class="text-2xl mb-2">{{ $mv[0] }}</p>
                            <p class="font-bold text-dark mb-1">{{ $mv[1] }}</p>
                            <p class="text-gray-500 text-sm">{{ $mv[2] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-4">
                @foreach([
                    ['2022', 'Naissance du concept', 'Face aux besoins non couverts sur le terrain, l\'idée d\'AgriFish prend forme après plusieurs années d\'expérience en conseil agricole.'],
                    ['2023', 'Développement de la plateforme', 'Premier prototype développé avec des tests utilisateurs auprès de coopératives agricoles en Côte d\'Ivoire.'],
                    ['2024', 'Lancement officiel', 'AgriFish est officiellement lancée et accueille ses premiers clients. Premiers projets accompagnés avec succès.'],
                    ['2025', 'Expansion régionale', 'Extension du service à 12 pays africains. Intégration des paiements mobiles locaux et lancement du pôle formation.'],
                ] as $i => $step)
                    <div class="flex items-start gap-5">
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 rounded-full {{ $i % 2 === 0 ? 'bg-primary' : 'bg-gold' }} flex items-center justify-center shrink-0">
                                <span class="text-white font-bold text-xs">{{ $step[0] }}</span>
                            </div>
                            @if(!$loop->last)
                                <div class="w-0.5 h-8 bg-gray-200 mt-2"></div>
                            @endif
                        </div>
                        <div class="pb-6">
                            <p class="font-bold text-dark">{{ $step[1] }}</p>
                            <p class="text-gray-500 text-sm mt-1 leading-relaxed">{{ $step[2] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Valeurs --}}
<section class="py-24 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider">Ce qui nous guide</span>
            <h2 class="text-3xl sm:text-4xl font-bold text-dark mt-2">Nos valeurs fondamentales</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['🤝', 'Proximité',     'Nous sommes présents là où nos clients ont besoin de nous, sur le terrain et en ligne.'],
                ['💡', 'Innovation',    'Nous adoptons les meilleures technologies au service des réalités africaines.'],
                ['🏆', 'Excellence',    'Chaque conseil, chaque formation, chaque projet est livré avec le plus haut niveau de qualité.'],
                ['🌍', 'Impact',        'Notre succès se mesure à l\'impact positif sur les revenus et conditions de vie des producteurs.'],
            ] as $val)
                <div class="bg-white rounded-2xl p-7 border border-gray-100 card-hover text-center">
                    <p class="text-4xl mb-4">{{ $val[0] }}</p>
                    <h3 class="font-bold text-dark text-lg mb-2">{{ $val[1] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $val[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Équipe --}}
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider">Les personnes derrière AgriFish</span>
            <h2 class="text-3xl sm:text-4xl font-bold text-dark mt-2">Notre équipe d'experts</h2>
            <p class="text-gray-500 mt-4 max-w-xl mx-auto">Des agronomes, ingénieurs, formateurs et experts numériques réunis autour d'une même vision.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach([
                ['Kofi Asante',      'Fondateur & DG',                '12 ans d\'expérience en agronomie tropicale'],
                ['Aminata Diallo',   'Directrice technique',           'Ingénieure en systèmes d\'information agricoles'],
                ['Jean-Pierre Bah',  'Responsable formation',          'Expert en pédagogie digitale et aquaculture'],
                ['Fatou Coulibaly',  'Responsable clientèle',          'Spécialiste accompagnement coopératives'],
            ] as $member)
                <div class="text-center card-hover">
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary-100 to-primary-light mx-auto mb-4 flex items-center justify-center">
                        <span class="text-3xl font-bold text-white">{{ substr($member[0], 0, 1) }}</span>
                    </div>
                    <h3 class="font-bold text-dark">{{ $member[0] }}</h3>
                    <p class="text-primary text-sm font-medium mt-0.5">{{ $member[1] }}</p>
                    <p class="text-gray-400 text-sm mt-2">{{ $member[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<x-cta-banner
    title="Travaillons ensemble"
    subtitle="Que vous soyez producteur, investisseur ou coopérative, AgriFish a une solution pour vous."
    btnLabel="Prendre contact"
    btnRoute="contact"
/>

@endsection
