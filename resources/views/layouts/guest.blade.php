<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <meta name="theme-color" content="#1b4332">

    <title>{{ $title ?? 'Espace client' }} | AgriFish</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white">
    <div class="grid min-h-screen lg:grid-cols-[1fr_1.05fr]">
        <div class="flex flex-col px-site py-6 sm:py-8">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" aria-label="AgriFish, accueil"><x-logo /></a>
                <a href="{{ route('home') }}" class="text-sm text-muted hover:text-dark">Retour au site</a>
            </div>

            <main class="flex flex-1 items-center justify-center py-12">
                <div class="w-full max-w-[400px]">
                    {{ $slot }}
                </div>
            </main>

            <p class="text-sm text-muted">Besoin d’aide ? <a href="{{ route('contact') }}" class="text-dark underline underline-offset-2">Contactez-nous</a></p>
        </div>

        <div class="relative hidden p-3 lg:block">
            <div class="sticky top-3 h-[calc(100vh-1.5rem)] overflow-hidden rounded-panel">
                <x-photo name="producteurs-tomates" eager sizes="50vw" alt="Deux producteurs inspectent des plants de tomates sur une parcelle irriguée." />
                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-night/85 via-night/40 to-transparent px-10 pb-10 pt-32">
                    <p class="max-w-md font-serif text-3xl leading-tight text-white">Vos demandes, vos documents et les échanges avec votre expert, au même endroit.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
