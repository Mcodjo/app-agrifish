@extends('layouts.app')

@section('title', 'Page introuvable')

@section('content')

<section class="hero-gradient min-h-screen flex items-center justify-center relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-primary-light/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-gold/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-2xl mx-auto px-4 text-center relative z-10 py-20">
        {{-- Code 404 --}}
        <div class="relative mb-8">
            <p class="text-[9rem] sm:text-[12rem] font-extrabold text-white/10 leading-none select-none">404</p>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-7xl">🌾</span>
            </div>
        </div>

        <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">
            Page introuvable
        </h1>
        <p class="text-white/70 text-lg leading-relaxed mb-10 max-w-md mx-auto">
            Oups ! La page que vous cherchez semble avoir été récoltée avant vous. Elle a peut-être été déplacée ou supprimée.
        </p>

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
                Contacter le support
            </a>
        </div>

        {{-- Liens rapides --}}
        <div class="mt-14">
            <p class="text-white/50 text-sm mb-5">Vous cherchiez peut-être :</p>
            <div class="flex flex-wrap gap-3 justify-center">
                @foreach([
                    ['route' => 'services',  'label' => 'Nos services'],
                    ['route' => 'formation', 'label' => 'Formations'],
                    ['route' => 'about',     'label' => 'À propos'],
                    ['route' => 'contact',   'label' => 'Contact'],
                ] as $link)
                    <a href="{{ route($link['route']) }}"
                       class="px-4 py-2 bg-white/10 border border-white/20 text-white text-sm rounded-lg hover:bg-white/20 transition-colors">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

@endsection
