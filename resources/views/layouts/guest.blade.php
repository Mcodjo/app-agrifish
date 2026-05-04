<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AgriFish') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-[#f5f0e8]">
    <div class="min-h-screen grid lg:grid-cols-[1.1fr_0.9fr]">
        <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-primary-dark via-primary to-primary-light text-white p-12 xl:p-16 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="relative z-10">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 mb-10">
                    <x-application-logo class="w-12 h-12 text-white" />
                    <span class="text-2xl font-bold tracking-tight">Agri<span class="text-gold-light">Fish</span></span>
                </a>

                <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md text-white text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-8 border border-white/20">
                    Conseil · Formation · Suivi de projet
                </span>

                <h1 class="text-4xl xl:text-5xl font-extrabold leading-tight mb-6 max-w-xl">
                    Un seul espace pour accéder à vos services AgriFish.
                </h1>

                <p class="text-white/75 text-lg leading-relaxed max-w-xl mb-10">
                    Connectez-vous pour suivre vos demandes, retrouver vos documents et gérer vos projets agricoles ou aquacoles en toute simplicité.
                </p>

                <div class="grid sm:grid-cols-3 gap-4 max-w-2xl">
                    <div class="bg-white/10 border border-white/15 rounded-2xl p-4">
                        <div class="text-gold-light font-extrabold text-2xl mb-1">24h</div>
                        <div class="text-sm text-white/70">Réponse moyenne</div>
                    </div>
                    <div class="bg-white/10 border border-white/15 rounded-2xl p-4">
                        <div class="text-gold-light font-extrabold text-2xl mb-1">100%</div>
                        <div class="text-sm text-white/70">Accès mobile</div>
                    </div>
                    <div class="bg-white/10 border border-white/15 rounded-2xl p-4">
                        <div class="text-gold-light font-extrabold text-2xl mb-1">12</div>
                        <div class="text-sm text-white/70">Pays couverts</div>
                    </div>
                </div>
            </div>

            <p class="relative z-10 text-sm text-white/55">© {{ date('Y') }} AgriFish. Conçu pour l'agriculture africaine.</p>
        </div>

        <div class="flex items-center justify-center px-4 py-10 sm:px-8 lg:px-12">
            <div class="w-full max-w-md">
                <div class="lg:hidden flex items-center justify-center mb-8">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                        <x-application-logo class="w-11 h-11 text-primary-dark" />
                        <span class="text-2xl font-bold tracking-tight text-primary-dark">Agri<span class="text-gold">Fish</span></span>
                    </a>
                </div>

                <div class="bg-white shadow-2xl rounded-3xl border border-white/70 p-6 sm:p-8">
                    {{ $slot }}
                </div>

                <div class="text-center mt-6 text-sm text-gray-600">
                    <a href="{{ route('home') }}" class="font-semibold text-primary hover:text-primary-dark">Retour à l'accueil</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
