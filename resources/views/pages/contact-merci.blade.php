@extends('layouts.app')

@section('title', 'Message envoyé')
@section('meta_description', 'Votre message a bien été envoyé à l’équipe AgriFish.')

@section('content')
<section class="max-w-content px-site py-20 sm:py-28">
    <div class="mx-auto max-w-2xl text-center">
        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary-50 text-primary-800">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12.5 4.5 4.5L19 7.5"/></svg>
        </span>
        <h1 class="display-lg mt-8">Merci, votre message est bien arrivé</h1>
        <p class="lead mx-auto mt-5 max-w-lg">Nous vous répondons sous {{ config('site.contact.delai') }}, à l’adresse e-mail indiquée dans le formulaire.</p>
        <div class="mt-10 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ route('home') }}" class="btn-primary">Retour à l’accueil</a>
            <a href="{{ route('services') }}" class="btn-secondary">Voir nos services</a>
        </div>
    </div>

    <div class="mx-auto mt-16 max-w-3xl rounded-2xl border border-line p-6 sm:p-8">
        <div class="grid gap-6 sm:grid-cols-[1fr_auto] sm:items-center">
            <div>
                <h2 class="font-semibold text-dark">Vous avez un projet à suivre ?</h2>
                <p class="mt-1 text-[15px] leading-7 text-muted">Créez votre espace client pour déposer vos documents et suivre l’avancement de votre demande.</p>
            </div>
            <a href="{{ route('register') }}" class="btn-soft">Créer mon espace</a>
        </div>
    </div>
</section>
@endsection
