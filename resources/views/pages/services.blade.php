@extends('layouts.app')

@section('title', 'Nos services')
@section('meta_description', 'Conseil agricole, aquaculture, gestion de projets, études de faisabilité — Découvrez tous les services AgriFish pour l\'agriculture africaine.')

@section('content')

{{-- Hero --}}
<x-hero-inner
    badge="Notre expertise"
    title="Nos <span class='text-gold-light'>services</span>"
    subtitle="Une offre complète d'accompagnement agricole et aquacole pour structurer, optimiser et développer votre activité."
/>

{{-- Services Agriculture --}}
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-12">
            <div class="w-14 h-14 rounded-xl bg-primary-50 flex items-center justify-center shrink-0">
                <span class="text-3xl">🌱</span>
            </div>
            <div>
                <span class="text-primary font-semibold text-sm uppercase tracking-wider">Pôle 1</span>
                <h2 class="text-3xl font-bold text-dark">Agriculture</h2>
            </div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                [
                    'title' => 'Conseil & diagnostic agricole',
                    'desc'  => 'Analyse du sol, diagnostic des cultures, recommandations d\'intrants et de pratiques culturales adaptées à votre contexte.',
                    'items' => ['Analyse de sol et cartographie', 'Plan de fumure personnalisé', 'Choix variétal adapté', 'Gestion des nuisibles'],
                ],
                [
                    'title' => 'Gestion de projets agricoles',
                    'desc'  => 'Accompagnement complet de vos projets de la conception à la réalisation, avec suivi en temps réel sur notre plateforme.',
                    'items' => ['Élaboration du plan de projet', 'Suivi des étapes et jalons', 'Gestion documentaire', 'Rapport d\'avancement'],
                ],
                [
                    'title' => 'Études de faisabilité',
                    'desc'  => 'Analyses économiques, études de marché et plans d\'affaires pour sécuriser vos investissements agricoles.',
                    'items' => ['Étude de marché', 'Analyse coût-bénéfice', 'Plan d\'affaires bancable', 'Recherche de financement'],
                ],
                [
                    'title' => 'Suivi & monitoring terrain',
                    'desc'  => 'Visites régulières, observations terrain et rapports techniques pour garantir la performance de vos exploitations.',
                    'items' => ['Visites agronomiques périodiques', 'Photos & rapports terrain', 'Alertes et recommandations', 'Bilan de campagne'],
                ],
                [
                    'title' => 'Gestion post-récolte',
                    'desc'  => 'Techniques de conservation, transformation et mise en marché pour maximiser la valeur de votre production.',
                    'items' => ['Techniques de séchage', 'Stockage & conservation', 'Transformation locale', 'Accès aux marchés'],
                ],
                [
                    'title' => 'Business development',
                    'desc'  => 'Développement commercial, mise en relation avec des partenaires, investisseurs et acheteurs potentiels.',
                    'items' => ['Mise en réseau', 'Recherche de partenaires', 'Développement export', 'Accès investisseurs'],
                ],
            ] as $service)
                <div class="bg-cream rounded-2xl p-7 border border-gray-100 card-hover">
                    <h3 class="font-bold text-dark text-lg mb-3">{{ $service['title'] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-5">{{ $service['desc'] }}</p>
                    <ul class="space-y-2">
                        @foreach($service['items'] as $item)
                            <li class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Services Aquaculture --}}
<section class="py-24 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-12">
            <div class="w-14 h-14 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <span class="text-3xl">🐟</span>
            </div>
            <div>
                <span class="text-blue-600 font-semibold text-sm uppercase tracking-wider">Pôle 2</span>
                <h2 class="text-3xl font-bold text-dark">Aquaculture & Pisciculture</h2>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8 mb-12">
            <div class="bg-white rounded-2xl p-8 border border-gray-100 card-hover">
                <h3 class="font-bold text-dark text-xl mb-4">Création & aménagement de sites</h3>
                <p class="text-gray-500 leading-relaxed mb-5">
                    De la sélection du site à la construction des bassins, nous accompagnons chaque étape de la création de votre ferme piscicole.
                </p>
                <div class="grid grid-cols-2 gap-3">
                    @foreach(['Étude de site', 'Conception des bassins', 'Choix des espèces', 'Mise en eau & alevinage'] as $item)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <div class="w-2 h-2 bg-blue-400 rounded-full shrink-0"></div>
                            {{ $item }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-2xl p-8 border border-gray-100 card-hover">
                <h3 class="font-bold text-dark text-xl mb-4">Suivi technique & sanitaire</h3>
                <p class="text-gray-500 leading-relaxed mb-5">
                    Surveillance de la qualité de l'eau, gestion sanitaire des élevages et optimisation des ratios alimentaires pour des productions rentables.
                </p>
                <div class="grid grid-cols-2 gap-3">
                    @foreach(['Qualité de l\'eau', 'Gestion sanitaire', 'Plans d\'alimentation', 'Prévention maladies'] as $item)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <div class="w-2 h-2 bg-blue-400 rounded-full shrink-0"></div>
                            {{ $item }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-2xl p-8 border border-gray-100 card-hover">
                <h3 class="font-bold text-dark text-xl mb-4">Transformation & commercialisation</h3>
                <p class="text-gray-500 leading-relaxed mb-5">
                    Optimisation de la chaîne de valeur post-récolte : fumage, congélation, emballage et mise en marché de vos produits aquatiques.
                </p>
                <div class="grid grid-cols-2 gap-3">
                    @foreach(['Techniques de fumage', 'Chaîne du froid', 'Emballage & packaging', 'Accès marchés locaux'] as $item)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <div class="w-2 h-2 bg-blue-400 rounded-full shrink-0"></div>
                            {{ $item }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-2xl p-8 border border-gray-100 card-hover">
                <h3 class="font-bold text-dark text-xl mb-4">Formation spécialisée</h3>
                <p class="text-gray-500 leading-relaxed mb-5">
                    Programmes de formation adaptés aux aquaculteurs, du débutant à l'exploitant professionnel, en présentiel ou en ligne.
                </p>
                <div class="grid grid-cols-2 gap-3">
                    @foreach(['Bases de pisciculture', 'Gestion d\'élevage', 'Nutrition des poissons', 'Business aquacole'] as $item)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <div class="w-2 h-2 bg-blue-400 rounded-full shrink-0"></div>
                            {{ $item }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Process --}}
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider">Comment ça marche</span>
            <h2 class="text-3xl sm:text-4xl font-bold text-dark mt-2">Notre processus en 4 étapes</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach([
                ['01', 'Prise de contact', 'Remplissez notre formulaire ou appelez-nous. Un expert vous contacte sous 24h.'],
                ['02', 'Diagnostic', 'Analyse de votre situation, vos besoins et vos objectifs pour construire une offre sur mesure.'],
                ['03', 'Accompagnement', 'Déploiement du plan d\'action avec suivi régulier via votre espace client personnel.'],
                ['04', 'Résultats', 'Évaluation des résultats, ajustements et rapport de clôture avec recommandations futures.'],
            ] as $step)
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl bg-primary flex items-center justify-center mx-auto mb-5">
                        <span class="text-2xl font-extrabold text-white">{{ $step[0] }}</span>
                    </div>
                    <h3 class="font-bold text-dark text-lg mb-2">{{ $step[1] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $step[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<x-cta-banner
    title="Besoin d'un accompagnement personnalisé ?"
    subtitle="Contactez-nous pour une analyse gratuite de votre projet agricole ou aquacole."
    btnLabel="Demander un devis gratuit"
    btnRoute="contact"
/>

@endsection
