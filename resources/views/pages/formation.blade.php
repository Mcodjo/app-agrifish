@extends('layouts.app')

@section('title', 'Formation')
@section('meta_description', 'Centre de formation digitale AgriFish : apprenez l\'agriculture moderne, la pisciculture et la gestion d\'exploitation. Certificats téléchargeables.')

@section('content')

{{-- Hero --}}
<section class="hero-gradient pt-32 pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-block px-4 py-1.5 bg-white/10 text-gold-light text-sm font-medium rounded-full border border-gold/30 mb-5">Pôle Formation Digitale</span>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-white leading-tight mb-5">
                    Formez-vous à <span class="text-gold-light">l'agriculture moderne</span> depuis chez vous
                </h1>
                <p class="text-white/75 text-lg leading-relaxed mb-8">
                    Des modules vidéo professionnels, des supports PDF téléchargeables et des certificats reconnus. Apprenez à votre rythme, depuis n'importe quel appareil.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#formations"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-gold text-white font-semibold rounded-xl hover:bg-gold-dark">
                        Voir les formations
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                    <a href="{{ route('contact') }}"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3.5 border-2 border-white/30 text-white font-semibold rounded-xl hover:bg-white/10">
                        S'inscrire en groupe
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['🎓', '25+', 'Modules disponibles'],
                    ['🏆', '100%', 'En ligne & mobile'],
                    ['📜', '2000+', 'Diplômés'],
                    ['🌍', '12', 'Pays atteints'],
                ] as $stat)
                    <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6 text-center">
                        <p class="text-3xl mb-1">{{ $stat[0] }}</p>
                        <p class="text-2xl font-extrabold text-gold-light">{{ $stat[1] }}</p>
                        <p class="text-white/60 text-sm mt-1">{{ $stat[2] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Avantages --}}
<section class="py-16 bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['📱', 'Mobile-first', 'Accessible sur smartphone, même sans internet stable.'],
                ['🎬', 'Vidéos HD', 'Cours vidéo professionnels avec formateurs experts.'],
                ['📄', 'Supports PDF', 'Fiches pratiques et guides téléchargeables.'],
                ['📜', 'Certificats', 'Certificats officiels téléchargeables à la fin de chaque module.'],
            ] as $av)
                <div class="flex items-start gap-4">
                    <span class="text-3xl">{{ $av[0] }}</span>
                    <div>
                        <p class="font-bold text-dark">{{ $av[1] }}</p>
                        <p class="text-gray-500 text-sm mt-1">{{ $av[2] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Catalogue formations --}}
<section id="formations" class="py-24 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider">Notre catalogue</span>
            <h2 class="text-3xl sm:text-4xl font-bold text-dark mt-2 mb-4">Formations disponibles</h2>
            <p class="text-gray-500 max-w-xl mx-auto">Des formations conçues par des experts pour des résultats concrets sur le terrain.</p>
        </div>

        {{-- Filtres Alpine.js --}}
        <div x-data="{ activeFilter: 'Tout' }" class="space-y-10">
            <div class="flex flex-wrap gap-3 justify-center">
                @foreach(['Tout', 'Agriculture', 'Aquaculture', 'Gestion', 'Business'] as $cat)
                    <button @click="activeFilter = '{{ $cat }}'"
                            :class="activeFilter === '{{ $cat }}' ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-200 hover:border-primary hover:text-primary'"
                            class="px-5 py-2 rounded-full text-sm font-medium border transition-colors">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6" x-cloak>
                @foreach([
                    ['🌽', 'Agriculture', 'Maraîchage tropical intensif', 'Techniques modernes de maraîchage adapté au climat tropical africain.', '6 modules', '4h 30min', 'Débutant', false],
                    ['🐟', 'Aquaculture', 'Pisciculture en bassin', 'Créer et gérer une ferme piscicole profitable de A à Z.', '8 modules', '6h 00min', 'Débutant', true],
                    ['🌾', 'Agriculture', 'Gestion de l\'irrigation', 'Systèmes d\'irrigation efficaces pour les petites et moyennes exploitations.', '5 modules', '3h 15min', 'Intermédiaire', false],
                    ['📊', 'Gestion', 'Gestion financière agricole', 'Tiens ta comptabilité, calcule ta rentabilité et pilote ton exploitation.', '7 modules', '5h 00min', 'Intermédiaire', true],
                    ['🦐', 'Aquaculture', 'Crevetticulture en eau douce', 'Introduction à l\'élevage de crevettes d\'eau douce pour les marchés locaux.', '6 modules', '4h 00min', 'Avancé', false],
                    ['💼', 'Business', 'Plan d\'affaires agricole', 'Rédiger un business plan solide pour obtenir des financements agricoles.', '5 modules', '3h 30min', 'Tout niveau', true],
                ] as $course)
                    <div x-show="activeFilter === 'Tout' || activeFilter === '{{ $course[1] }}'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="bg-white rounded-2xl overflow-hidden border border-gray-100 card-hover">
                        <div class="h-40 bg-gradient-to-br from-primary-dark to-primary flex items-center justify-center relative">
                            <span class="text-6xl">{{ $course[0] }}</span>
                            @if($course[7])
                                <span class="absolute top-4 right-4 bg-gold text-white text-xs font-bold px-3 py-1 rounded-full">Populaire</span>
                            @endif
                            <span class="absolute bottom-4 left-4 bg-white/20 text-white text-xs font-medium px-3 py-1 rounded-full">{{ $course[1] }}</span>
                        </div>
                        <div class="p-6">
                            <h3 class="font-bold text-dark text-lg mb-2">{{ $course[2] }}</h3>
                            <p class="text-gray-500 text-sm leading-relaxed mb-5">{{ $course[3] }}</p>
                            <div class="flex items-center gap-4 text-gray-400 text-xs mb-5">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $course[4] }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $course[5] }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                    {{ $course[6] }}
                                </span>
                            </div>
                            <a href="{{ route('contact') }}" class="block w-full text-center py-3 bg-primary text-white font-semibold rounded-lg text-sm hover:bg-primary-dark">
                                S'inscrire à cette formation
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Comment ça marche --}}
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-dark">Comment fonctionne la formation ?</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach([
                ['01', '📋', 'Choisissez votre formation', 'Parcourez le catalogue et sélectionnez la formation adaptée à vos besoins.'],
                ['02', '💳', 'Inscrivez-vous & payez', 'Paiement sécurisé par Mobile Money, carte bancaire ou virement.'],
                ['03', '📱', 'Accédez au contenu', 'Regardez les vidéos, téléchargez les supports et faites les exercices pratiques.'],
                ['04', '🏆', 'Obtenez votre certificat', 'Validez le module final et téléchargez votre certificat officiel AgriFish.'],
            ] as $step)
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl bg-cream border-2 border-primary-100 flex flex-col items-center justify-center mx-auto mb-5">
                        <span class="text-xs font-bold text-primary-light">{{ $step[0] }}</span>
                        <span class="text-2xl">{{ $step[1] }}</span>
                    </div>
                    <h3 class="font-bold text-dark mb-2">{{ $step[2] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $step[3] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<x-cta-banner
    title="Commencez votre formation aujourd'hui"
    subtitle="Rejoignez 2000+ professionnels africains déjà formés par AgriFish."
    btnLabel="S'inscrire maintenant"
    btnRoute="contact"
/>

@endsection
