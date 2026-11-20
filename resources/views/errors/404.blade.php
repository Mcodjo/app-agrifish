@extends('layouts.app')

@section('title', 'Page introuvable')

@section('content')
<section class="max-w-content px-site py-20 sm:py-28">
    <div class="max-w-2xl">
        <p class="text-sm font-medium text-muted">Erreur 404</p>
        <h1 class="display-lg mt-5">Cette page n’existe pas ou a été déplacée</h1>
        <p class="lead mt-5">Vérifiez l’adresse saisie, ou poursuivez votre visite à partir de l’une des pages ci-dessous.</p>
        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('home') }}" class="btn-primary">Retour à l’accueil</a>
            <a href="{{ route('contact') }}" class="btn-secondary">Nous contacter</a>
        </div>
    </div>

    <ul class="mt-16 grid gap-x-8 border-t border-line sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['services', 'Services', 'Agriculture, aquaculture, études techniques'],
            ['formation', 'Formation', 'Catalogue et sessions de groupe'],
            ['case-studies', 'Réalisations', 'Exemples de missions'],
            ['about', 'À propos', 'Notre équipe et notre méthode'],
        ] as [$route, $titre, $texte])
            <li class="border-b border-line">
                <a href="{{ route($route) }}" class="block py-5 hover:text-primary-800">
                    <span class="font-semibold text-dark">{{ $titre }}</span>
                    <span class="mt-1 block text-sm text-muted">{{ $texte }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</section>
@endsection
