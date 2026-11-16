<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="@yield('meta_description', 'AgriFish — Plateforme digitale pour l\'agriculture et l\'aquaculture en Afrique')">
    <meta name="keywords" content="AgriFish, agriculture, aquaculture, études techniques, projets agricoles, Afrique">
    <meta property="og:site_name" content="AgriFish">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', 'AgriFish')))" />
    <meta property="og:description" content="@yield('og_description', trim($__env->yieldContent('meta_description', 'AgriFish — Plateforme digitale pour l\'agriculture et l\'aquaculture en Afrique')))" />
    <meta property="og:url" content="@yield('og_url', url()->current())" />
    <meta property="og:image" content="@yield('og_image', asset('build/assets/agrifish-og.png'))" />
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', trim($__env->yieldContent('title', 'AgriFish')))" />
    <meta name="twitter:description" content="@yield('og_description', trim($__env->yieldContent('meta_description', 'AgriFish — Plateforme digitale pour l\'agriculture et l\'aquaculture en Afrique')))" />
    <link rel="canonical" href="@yield('og_url', url()->current())">
    <title>@yield('title', 'AgriFish') — Plateforme digitale agricole</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-gray-900 pt-16 md:pt-20 overflow-x-hidden">

    <a href="#contenu" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[70] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:font-semibold focus:text-primary focus:shadow-lg">Aller au contenu</a>

    @php
        $navLinks = [
            ['route' => 'home',         'label' => 'Accueil'],
            ['route' => 'about',        'label' => 'À propos'],
            ['route' => 'services',     'label' => 'Services'],
            ['route' => 'formation',    'label' => 'Formation'],
            ['route' => 'case-studies', 'label' => 'Réalisations'],
            ['route' => 'contact',      'label' => 'Contact'],
        ];
        $user = auth()->user();
        $roleLabel = $user ? ['admin' => 'Administrateur', 'expert' => 'Expert', 'client' => 'Client'][$user->role] ?? 'Client' : null;
        $initials = $user ? collect(explode(' ', $user->name))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') : null;
    @endphp

    <header x-data="{ open: false, scrolled: false }"
            x-init="scrolled = window.scrollY > 10; window.addEventListener('scroll', () => scrolled = window.scrollY > 10)"
            @keydown.escape.window="open = false"
            :class="scrolled ? 'shadow-md' : ''"
            class="fixed inset-x-0 top-0 z-50 border-b border-gray-100 bg-white/95 backdrop-blur transition-shadow duration-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between md:h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="AgriFish — accueil">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary shadow-sm">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2c4.4 0 8 3.6 8 8 0 2.1-.8 4-2.1 5.4L12 22l-5.9-6.6C4.8 14 4 12.1 4 10c0-4.4 3.6-8 8-8z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v10M7 12h10"/>
                        </svg>
                    </span>
                    <span class="text-xl font-extrabold tracking-tight text-primary-dark">Agri<span class="text-gold">Fish</span></span>
                </a>

                <nav class="hidden items-center gap-1 lg:flex" aria-label="Navigation principale">
                    @foreach($navLinks as $link)
                        @php $active = request()->routeIs($link['route']); @endphp
                        <a href="{{ route($link['route']) }}" @if($active) aria-current="page" @endif
                           class="relative rounded-lg px-3.5 py-2 text-sm font-medium transition {{ $active ? 'text-primary' : 'text-gray-600 hover:bg-cream hover:text-primary-dark' }}">
                            {{ $link['label'] }}
                            @if($active)<span class="absolute inset-x-3.5 -bottom-[1px] h-0.5 rounded-full bg-gold"></span>@endif
                        </a>
                    @endforeach
                </nav>

                <div class="hidden items-center gap-3 lg:flex">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-white shadow-sm transition {{ request()->routeIs('dashboard', 'projects.*', 'requests.*', 'admin.*') ? 'bg-primary-dark' : 'bg-primary hover:bg-primary-dark' }}">Mon espace</a>
                        <div x-data="{ menu: false }" class="relative" @click.outside="menu = false">
                            <button type="button" @click="menu = !menu" :aria-expanded="menu" aria-haspopup="true"
                                    class="flex items-center gap-2 rounded-full py-1 pl-1 pr-2 transition hover:bg-cream">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gold/15 text-sm font-bold text-gold-dark">{{ $initials }}</span>
                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="menu" x-cloak x-transition.origin.top.right
                                 class="absolute right-0 mt-2 w-60 overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-gray-100">
                                <div class="border-b border-gray-100 px-4 py-3">
                                    <p class="truncate text-sm font-semibold text-dark">{{ $user->name }}</p>
                                    <p class="truncate text-xs text-gray-500">{{ $roleLabel }} · {{ $user->email }}</p>
                                </div>
                                <div class="p-1.5 text-sm">
                                    <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-gray-700 hover:bg-cream">Tableau de bord</a>
                                    @if($user->isAdmin())
                                        <a href="{{ route('admin.requests.index') }}" class="block rounded-lg px-3 py-2 text-gray-700 hover:bg-cream">Gestion des demandes</a>
                                    @endif
                                    <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-gray-700 hover:bg-cream">Mon profil</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-red-600 hover:bg-red-50">Déconnexion</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-semibold text-gray-700 hover:text-primary">Connexion</a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-gold px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gold-dark">Créer un compte</a>
                    @endauth
                </div>

                <div class="flex items-center gap-2 lg:hidden">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-white">Mon espace</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-white">Connexion</a>
                    @endauth
                    <button type="button" @click="open = !open" :aria-expanded="open" aria-label="Menu" class="rounded-lg p-2 text-gray-700 hover:bg-cream">
                        <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="open" x-cloak x-transition class="border-t border-gray-100 bg-white px-4 pb-5 shadow-lg lg:hidden">
            <nav class="py-2" aria-label="Navigation mobile">
                @foreach($navLinks as $link)
                    <a href="{{ route($link['route']) }}" class="flex items-center justify-between rounded-lg px-3 py-3 font-medium {{ request()->routeIs($link['route']) ? 'bg-cream text-primary' : 'text-gray-700' }}">
                        {{ $link['label'] }}
                        @if(request()->routeIs($link['route']))<span class="h-2 w-2 rounded-full bg-gold"></span>@endif
                    </a>
                @endforeach
            </nav>
            <div class="flex flex-col gap-2 border-t border-gray-100 pt-4">
                @auth
                    <p class="px-3 pb-1 text-xs text-gray-500">Connecté : <span class="font-semibold text-dark">{{ $user->name }}</span> ({{ $roleLabel }})</p>
                    @if($user->isAdmin())
                        <a href="{{ route('admin.requests.index') }}" class="rounded-lg px-3 py-2.5 font-medium text-gray-700 hover:bg-cream">Gestion des demandes</a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="rounded-lg px-3 py-2.5 font-medium text-gray-700 hover:bg-cream">Mon profil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2.5 text-left font-medium text-red-600 hover:bg-red-50">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg border border-gray-200 px-5 py-3 text-center font-semibold text-primary-dark">Connexion</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-gold px-5 py-3 text-center font-semibold text-white">Créer un compte</a>
                @endauth
            </div>
        </div>
    </header>

    <x-flash />

    <main id="contenu">
        @yield('content')
    </main>

    <footer class="bg-dark text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2c4.4 0 8 3.6 8 8 0 2.1-.8 4-2.1 5.4L12 22l-5.9-6.6C4.8 14 4 12.1 4 10c0-4.4 3.6-8 8-8z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v10M7 12h10"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold">Agri<span class="text-gold">Fish</span></span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-5">La plateforme digitale intelligente pour l'agriculture et l'aquaculture en Afrique.</p>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-5 text-sm uppercase tracking-wider">Navigation</h4>
                    <ul class="space-y-3">
                        @foreach($navLinks as $link)
                            <li><a href="{{ route($link['route']) }}" class="text-gray-400 hover:text-gold text-sm">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-5 text-sm uppercase tracking-wider">Services</h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li>Conseil agricole</li>
                        <li>Gestion de projets</li>
                        <li>Aquaculture & pisciculture</li>
                        <li>Études de faisabilité</li>
                        <li>Formation digitale</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-5 text-sm uppercase tracking-wider">Contact</h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li class="flex items-start gap-2"><span class="text-gold">•</span><span>Afrique de l'Ouest</span></li>
                        <li class="flex items-start gap-2"><span class="text-gold">•</span><span>contact@agrifish.africa</span></li>
                        <li class="flex items-start gap-2"><span class="text-gold">•</span><span>+225 XX XX XX XX</span></li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-gray-500 text-sm">© {{ date('Y') }} AgriFish. Tous droits réservés.</p>
                <div class="flex items-center gap-5 text-sm">
                    <a href="{{ route('mentions-legales') }}" class="text-gray-500 hover:text-gold">Mentions légales</a>
                    <a href="{{ route('politique-confidentialite') }}" class="text-gray-500 hover:text-gold">Confidentialité</a>
                    <a href="{{ route('contact') }}" class="text-gray-500 hover:text-gold">Contact</a>
                </div>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/2250000000000?text=Bonjour%20AgriFish%2C%20je%20voudrais%20des%20informations%20sur%20vos%20services."
       target="_blank" rel="noopener noreferrer"
       aria-label="Nous contacter sur WhatsApp"
       class="fixed bottom-5 right-5 z-40 w-14 h-14 bg-[#25D366] rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition-transform"
       title="Nous contacter sur WhatsApp">
        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20.52 3.48A11.79 11.79 0 0 0 12.06 0C5.4 0 .01 5.39 0 12.05c0 2.12.55 4.2 1.6 6.03L0 24l6.08-1.59a11.98 11.98 0 0 0 5.97 1.52h.01c6.66 0 12.05-5.39 12.05-12.05a11.8 11.8 0 0 0-3.59-8.4zM12.06 22.08h-.01a9.97 9.97 0 0 1-5.1-1.4l-.37-.22-3.61.95.96-3.53-.24-.38a9.93 9.93 0 0 1-1.52-5.27C2.17 6.8 6.75 2.22 12.06 2.22a9.8 9.8 0 0 1 6.98 2.89 9.82 9.82 0 0 1 2.88 6.94c0 5.31-4.56 9.99-9.86 9.99zm5.7-7.77c-.31-.15-1.83-.9-2.11-1s-.48-.15-.68.15-.78 1-.96 1.2-.35.23-.66.08a8.12 8.12 0 0 1-2.39-1.47 9 9 0 0 1-1.66-2.06c-.17-.3-.02-.47.13-.62.13-.13.3-.35.44-.53.15-.17.2-.3.3-.5.1-.2.05-.38-.02-.53s-.68-1.66-.94-2.27c-.25-.6-.5-.52-.68-.53h-.58c-.2 0-.53.08-.81.38s-1.06 1.04-1.06 2.53 1.09 2.93 1.24 3.13c.15.2 2.13 3.26 5.16 4.57.72.31 1.28.5 1.71.64.72.23 1.37.2 1.88.12.57-.09 1.83-.75 2.09-1.47.26-.73.26-1.35.18-1.47-.08-.12-.28-.2-.58-.35z"/></svg>
    </a>
</body>
</html>
