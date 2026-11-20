<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('petition.title'))</title>
    <meta name="description" content="@yield('description', 'Pétition pour que hackathons, conférences et ateliers tech se tiennent aussi à Porto-Novo, ville capitale du Bénin.')">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ config('petition.title') }}">
    <meta property="og:description" content="Porto-Novo a des talents. Signez pour que les événements tech et de l’innovation viennent aussi dans la ville capitale.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:locale" content="fr_FR">
    <meta name="twitter:card" content="summary">
    <meta name="theme-color" content="#0b6b43">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=DM+Sans:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/petition.css') }}?v=1">
    @stack('head')
</head>
<body>
    <div class="flagbar" aria-hidden="true"><i></i><i></i><i></i></div>
    <nav class="topnav wrap">
        <a href="{{ route('petition.index') }}" class="brand">Tech à Porto-Novo</a>
        <div class="links">
            <a href="{{ route('petition.signatures') }}">Signataires</a>
            <a href="{{ route('petition.index') }}#signer" class="pill">Signer</a>
        </div>
    </nav>
    <main class="wrap">
        @yield('content')
    </main>
    <footer class="wrap site-footer">
        <p>Pétition citoyenne initiée par Maurice Michel Hotegni CODJO, Porto-Novo, Bénin.</p>
        <p>Vos données servent uniquement à cette pétition. Votre adresse e-mail n’est jamais publiée. Seuls le prénom, l’initiale du nom, la commune et le profil apparaissent publiquement, et seulement si vous l’acceptez.</p>
    </footer>
    @stack('scripts')
</body>
</html>
