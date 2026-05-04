@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])
@section('og_title', $seo['title'])
@section('og_description', $seo['description'])
@section('og_url', $seo['url'])

@section('content')
<section class="relative min-h-[75vh] overflow-hidden bg-gradient-to-br from-primary-dark via-primary to-primary-light text-white flex items-center">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 36px 36px;"></div>
    <div class="max-w-3xl mx-auto px-4 text-center relative z-10 py-20">
        <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full border border-white/20 bg-white/10">
            <svg class="h-12 w-12 text-gold-light" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h1 class="mt-8 text-4xl font-extrabold sm:text-5xl">Message envoyé</h1>
        <p class="mx-auto mt-5 max-w-2xl text-lg leading-8 text-white/78">Merci pour votre message. Nous l’avons bien reçu et nous vous répondrons sous 24 heures ouvrables.</p>

        <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:justify-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl bg-gold px-7 py-4 font-bold text-white transition hover:bg-gold-dark">Retour à l’accueil</a>
            <a href="{{ route('services') }}" class="inline-flex items-center justify-center rounded-xl border border-white/30 px-7 py-4 font-semibold text-white transition hover:bg-white/10">Voir nos services</a>
        </div>
    </div>
</section>
@endsection
