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
<body class="font-sans antialiased bg-white text-gray-900 pt-20 md:pt-24">

    <header x-data="{ open: false, scrolled: false }"
            x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 30)"
            :class="scrolled ? 'bg-white shadow-lg' : 'bg-transparent'"
            class="fixed top-0 inset-x-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18 py-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center shadow">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2c4.4 0 8 3.6 8 8 0 2.1-.8 4-2.1 5.4L12 22l-5.9-6.6C4.8 14 4 12.1 4 10c0-4.4 3.6-8 8-8z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v10M7 12h10"/>
                        </svg>
                    </div>
                    <span :class="scrolled ? 'text-primary-dark' : 'text-white'" class="text-xl font-bold tracking-tight transition-colors">
                        Agri<span class="text-gold">Fish</span>
                    </span>
                </a>

                @php
                    $navLinks = [
                        ['route' => 'home',         'label' => 'Accueil'],
                        ['route' => 'about',        'label' => 'À propos'],
                        ['route' => 'services',     'label' => 'Nos services'],
                        ['route' => 'formation',    'label' => 'Formation'],
                        ['route' => 'case-studies', 'label' => 'Impact'],
                        ['route' => 'contact',      'label' => 'Contact'],
                    ];
                @endphp

                <nav class="hidden md:flex items-center gap-1">
                    @foreach($navLinks as $link)
                        <a href="{{ route($link['route']) }}"
                           :class="scrolled ? 'text-gray-700 hover:text-primary' : 'text-white/90 hover:text-white'"
                           class="px-4 py-2 text-sm font-medium rounded-lg hover:bg-white/10 transition-all {{ request()->routeIs($link['route']) ? 'font-semibold' : '' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-semibold text-white bg-primary rounded-lg shadow hover:bg-primary-light">Mon espace</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-semibold text-primary-dark bg-white rounded-lg shadow hover:bg-cream">
                                Déconnexion
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-white/90 hover:text-white">Connexion</a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 bg-gold text-white text-sm font-semibold rounded-lg shadow hover:bg-gold-dark">
                            Inscription
                        </a>
                    @endauth
                </div>

                <div class="md:hidden flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" :class="scrolled ? 'bg-primary' : 'bg-white/20 border border-white/30'" class="px-3 py-1.5 text-white text-xs font-semibold rounded-lg transition-colors">Espace</a>
                    @else
                        <a href="{{ route('login') }}" :class="scrolled ? 'bg-primary' : 'bg-white/20 border border-white/30'" class="px-3 py-1.5 text-white text-xs font-semibold rounded-lg transition-colors">Connexion</a>
                    @endauth
                    <button @click="open = !open" :class="scrolled ? 'text-gray-700' : 'text-white'" class="p-2">
                        <svg x-show="!open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="open" x-transition class="md:hidden bg-primary-dark/95 backdrop-blur-sm px-4 pb-4">
            @foreach($navLinks as $link)
                <a href="{{ route($link['route']) }}" class="block px-4 py-3 text-white font-medium border-b border-white/10 hover:text-gold">{{ $link['label'] }}</a>
            @endforeach
            <div class="pt-4 flex flex-col gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="block text-center px-5 py-3 bg-gold text-white font-semibold rounded-lg">Mon espace</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-center px-5 py-3 border border-white/30 text-white font-semibold rounded-lg">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block text-center px-5 py-3 border border-white/30 text-white font-semibold rounded-lg">Connexion</a>
                    <a href="{{ route('register') }}" class="block text-center px-5 py-3 bg-gold text-white font-semibold rounded-lg">Inscription</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
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
       class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-[#25D366] rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition-transform"
       title="Nous contacter sur WhatsApp">
        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20.52 3.48A11.79 11.79 0 0 0 12.06 0C5.4 0 .01 5.39 0 12.05c0 2.12.55 4.2 1.6 6.03L0 24l6.08-1.59a11.98 11.98 0 0 0 5.97 1.52h.01c6.66 0 12.05-5.39 12.05-12.05a11.8 11.8 0 0 0-3.59-8.4zM12.06 22.08h-.01a9.97 9.97 0 0 1-5.1-1.4l-.37-.22-3.61.95.96-3.53-.24-.38a9.93 9.93 0 0 1-1.52-5.27C2.17 6.8 6.75 2.22 12.06 2.22a9.8 9.8 0 0 1 6.98 2.89 9.82 9.82 0 0 1 2.88 6.94c0 5.31-4.56 9.99-9.86 9.99zm5.7-7.77c-.31-.15-1.83-.9-2.11-1s-.48-.15-.68.15-.78 1-.96 1.2-.35.23-.66.08a8.12 8.12 0 0 1-2.39-1.47 9 9 0 0 1-1.66-2.06c-.17-.3-.02-.47.13-.62.13-.13.3-.35.44-.53.15-.17.2-.3.3-.5.1-.2.05-.38-.02-.53s-.68-1.66-.94-2.27c-.25-.6-.5-.52-.68-.53h-.58c-.2 0-.53.08-.81.38s-1.06 1.04-1.06 2.53 1.09 2.93 1.24 3.13c.15.2 2.13 3.26 5.16 4.57.72.31 1.28.5 1.71.64.72.23 1.37.2 1.88.12.57-.09 1.83-.75 2.09-1.47.26-.73.26-1.35.18-1.47-.08-.12-.28-.2-.58-.35z"/></svg>
    </a>
</body>
</html>
