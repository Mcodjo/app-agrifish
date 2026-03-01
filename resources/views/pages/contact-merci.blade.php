@extends('layouts.app')

@section('title', 'Message envoyé — Merci !')
@section('meta_description', 'Votre message a bien été envoyé à l\'équipe AgriFish. Nous vous répondrons sous 24 heures.')

@section('content')

<section class="hero-gradient min-h-screen flex items-center justify-center relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-primary-light/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-gold/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-2xl mx-auto px-4 text-center relative z-10 py-24">

        {{-- Icône succès animée --}}
        <div class="w-24 h-24 rounded-full bg-white/10 border-2 border-white/30 flex items-center justify-center mx-auto mb-8">
            <svg class="w-12 h-12 text-gold-light" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4">
            Message envoyé !
        </h1>
        <p class="text-white/75 text-lg leading-relaxed mb-3 max-w-lg mx-auto">
            Merci pour votre message. Notre équipe d'experts l'a bien reçu et vous répondra <strong class="text-gold-light">sous 24 heures ouvrables</strong>.
        </p>
        <p class="text-white/50 text-sm mb-12">
            Un email de confirmation a été envoyé à votre adresse.
        </p>

        {{-- Carte récapitulatif --}}
        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-6 mb-10 text-left">
            <h2 class="text-white font-semibold mb-4">En attendant notre réponse, vous pouvez :</h2>
            <ul class="space-y-3">
                @foreach([
                    ['🌿', 'Découvrir nos services agricoles', route('services')],
                    ['🎓', 'Explorer notre catalogue de formations', route('formation')],
                    ['🤝', 'En savoir plus sur notre équipe', route('about')],
                ] as $item)
                    <li>
                        <a href="{{ $item[2] }}"
                           class="flex items-center gap-3 text-white/80 hover:text-white transition-colors">
                            <span class="text-xl">{{ $item[0] }}</span>
                            <span class="text-sm">{{ $item[1] }}</span>
                            <svg class="w-4 h-4 ml-auto shrink-0 text-gold-light" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}"
               class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gold text-white font-bold rounded-xl text-lg hover:bg-gold-dark transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Retour à l'accueil
            </a>
            <a href="{{ route('contact') }}"
               class="inline-flex items-center justify-center gap-2 px-8 py-4 border-2 border-white/40 text-white font-semibold rounded-xl text-lg hover:bg-white/10 transition-colors">
                Envoyer un autre message
            </a>
        </div>
    </div>
</section>

@endsection
