@php
    $pageTitle = trim($__env->yieldContent('title'));
    $fullTitle = $pageTitle && $pageTitle !== 'Accueil'
        ? $pageTitle.' | AgriFish'
        : 'AgriFish | Conseil, études et formation agricoles et aquacoles';
    $description = trim($__env->yieldContent('meta_description')) ?: 'AgriFish accompagne les projets agricoles et aquacoles en Afrique de l’Ouest : conseil technique, études de faisabilité, formation et suivi de projet en ligne.';

    $navLinks = [
        ['route' => 'services', 'label' => 'Services'],
        ['route' => 'formation', 'label' => 'Formation'],
        ['route' => 'shop.index', 'label' => 'Boutique', 'pattern' => 'shop.*'],
        ['route' => 'case-studies', 'label' => 'Réalisations'],
        ['route' => 'about', 'label' => 'À propos'],
        ['route' => 'contact', 'label' => 'Contact'],
    ];
    $user = auth()->user();
    $roleLabel = $user?->roleLabel();
    $cartCount ??= 0;
    $initials = $user ? collect(explode(' ', $user->name))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') : null;
    $espace = $__env->hasSection('espace');
    $noindex = $espace || $__env->hasSection('noindex');
    $contact = config('site.contact');
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    @include('layouts.partials.head')
</head>
<body class="min-h-screen bg-white">
    <a href="#contenu" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[70] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:font-semibold focus:shadow-lg">Aller au contenu</a>

    <header x-data="{ open: false, scrolled: false }"
            x-init="scrolled = window.scrollY > 4"
            @scroll.window="scrolled = window.scrollY > 4"
            @keydown.escape.window="open = false"
            :class="scrolled || open ? 'border-line' : 'border-transparent'"
            class="sticky top-0 z-50 border-b bg-white transition-colors">
        <div class="max-w-content px-site">
            <div class="flex h-16 items-center justify-between gap-6 lg:h-[72px]">
                <a href="{{ route('home') }}" aria-label="AgriFish, accueil"><x-logo /></a>

                <nav class="hidden items-center gap-1 lg:flex" aria-label="Navigation principale">
                    @foreach($navLinks as $link)
                        @php $active = request()->routeIs($link['pattern'] ?? $link['route']); @endphp
                        <a href="{{ route($link['route']) }}" @if($active) aria-current="page" @endif
                           class="rounded-lg px-3 py-2 text-[15px] transition-colors {{ $active ? 'font-semibold text-dark' : 'text-muted hover:text-dark' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="hidden items-center gap-2 lg:flex">
                    <a href="{{ route('shop.cart') }}" class="relative flex h-10 w-10 items-center justify-center rounded-full text-dark transition hover:bg-cream" aria-label="Panier{{ $cartCount ? ', '.$cartCount.' article(s)' : '' }}">
                        <svg class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 7h12l1 13H5L6 7Zm3 0V6a3 3 0 0 1 6 0v1"/></svg>
                        @if($cartCount)
                            <span class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-primary-800 px-1 text-[11px] font-semibold text-white">{{ $cartCount }}</span>
                        @endif
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-soft px-4 py-2.5 text-sm">Mon espace</a>
                        <div x-data="{ menu: false }" class="relative" @click.outside="menu = false">
                            <button type="button" @click="menu = !menu" :aria-expanded="menu" aria-haspopup="true" aria-label="Menu du compte"
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-50 text-sm font-semibold text-primary-800 transition hover:bg-primary-100">
                                {{ $initials }}
                            </button>
                            <div x-show="menu" x-cloak x-transition.origin.top.right
                                 class="absolute right-0 mt-2 w-64 overflow-hidden rounded-xl border border-line bg-white shadow-lg">
                                <div class="border-b border-line px-4 py-3">
                                    <p class="truncate text-sm font-semibold text-dark">{{ $user->name }}</p>
                                    <p class="truncate text-xs text-muted">{{ $roleLabel }} · {{ $user->email }}</p>
                                </div>
                                <div class="p-1.5 text-sm">
                                    <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-dark hover:bg-cream">Mon espace</a>
                                    @if($user->canManageShop())
                                        <a href="{{ route('shop-admin.orders.index') }}" class="block rounded-lg px-3 py-2 text-dark hover:bg-cream">Gestion de la boutique</a>
                                    @else
                                        <a href="{{ route('account.orders.index') }}" class="block rounded-lg px-3 py-2 text-dark hover:bg-cream">Mes commandes</a>
                                    @endif
                                    <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-dark hover:bg-cream">Mon profil</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-red-700 hover:bg-red-50">Se déconnecter</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn-soft px-4 py-2.5 text-sm">Espace client</a>
                        <a href="{{ route('contact') }}" class="btn-primary px-4 py-2.5 text-sm">Parler de mon projet</a>
                    @endauth
                </div>

                <div class="flex items-center gap-1 lg:hidden">
                <a href="{{ route('shop.cart') }}" class="relative rounded-lg p-2 text-dark" aria-label="Panier{{ $cartCount ? ', '.$cartCount.' article(s)' : '' }}">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 7h12l1 13H5L6 7Zm3 0V6a3 3 0 0 1 6 0v1"/></svg>
                    @if($cartCount)
                        <span class="absolute right-0 top-0 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-primary-800 px-1 text-[10px] font-semibold text-white">{{ $cartCount }}</span>
                    @endif
                </a>
                <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="menu-mobile" aria-label="Ouvrir le menu"
                        class="-mr-2 rounded-lg p-2 text-dark">
                    <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                    <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
                </div>
            </div>
        </div>

        <div id="menu-mobile" x-show="open" x-cloak x-collapse class="border-t border-line bg-white lg:hidden">
            <nav class="max-w-content px-site py-3" aria-label="Navigation mobile">
                @foreach($navLinks as $link)
                    <a href="{{ route($link['route']) }}" class="block border-b border-line py-3.5 text-[17px] {{ request()->routeIs($link['pattern'] ?? $link['route']) ? 'font-semibold text-dark' : 'text-dark' }}">{{ $link['label'] }}</a>
                @endforeach
                <div class="flex flex-col gap-2 py-5">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary">Mon espace</a>
                        <a href="{{ route('profile.edit') }}" class="btn-soft">Mon profil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn-secondary w-full">Se déconnecter</button>
                        </form>
                    @else
                        <a href="{{ route('contact') }}" class="btn-primary">Parler de mon projet</a>
                        <a href="{{ route('login') }}" class="btn-soft">Espace client</a>
                    @endauth
                </div>
            </nav>
        </div>
    </header>

    <x-flash />

    <main id="contenu">
        @yield('content')
    </main>

    @if($espace)
        <footer class="border-t border-line bg-white">
            <div class="max-w-content flex flex-col gap-3 px-site py-6 text-sm text-muted sm:flex-row sm:items-center sm:justify-between">
                <p>© {{ date('Y') }} AgriFish</p>
                <div class="flex gap-5">
                    <a href="{{ route('contact') }}" class="hover:text-dark">Assistance</a>
                    <a href="{{ route('cgv') }}" class="hover:text-dark">CGV</a>
                    <a href="{{ route('mentions-legales') }}" class="hover:text-dark">Mentions légales</a>
                    <a href="{{ route('politique-confidentialite') }}" class="hover:text-dark">Confidentialité</a>
                </div>
            </div>
        </footer>
    @else
        <footer class="mx-2 mb-2 overflow-hidden rounded-[20px] bg-night text-white sm:mx-3 sm:mb-3 sm:rounded-panel">
            @yield('cta')

            <div class="max-w-content px-site">
                <div class="grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1fr]">
                    <div class="max-w-xs">
                        <x-logo inverse />
                        <p class="mt-5 text-[15px] leading-7 text-white/60">Conseil technique, études de faisabilité et formation pour les projets agricoles et aquacoles en {{ $contact['zone'] }}.</p>
                        <div class="mt-5 space-y-1 text-[15px]">
                            <a href="mailto:{{ $contact['email'] }}" class="block text-white/85 hover:text-white">{{ $contact['email'] }}</a>
                            @if($contact['telephone'])
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contact['telephone']) }}" class="block text-white/85 hover:text-white">{{ $contact['telephone'] }}</a>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h2 class="text-sm text-white/45">Services</h2>
                        <ul class="mt-4 space-y-3 text-[15px]">
                            <li><a href="{{ route('services') }}#agriculture" class="text-white/85 hover:text-white">Agriculture</a></li>
                            <li><a href="{{ route('services') }}#aquaculture" class="text-white/85 hover:text-white">Aquaculture</a></li>
                            <li><a href="{{ route('services') }}#etudes" class="text-white/85 hover:text-white">Études techniques</a></li>
                            <li><a href="{{ route('formation') }}" class="text-white/85 hover:text-white">Formation</a></li>
                            <li><a href="{{ route('shop.index') }}" class="text-white/85 hover:text-white">Boutique</a></li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-sm text-white/45">AgriFish</h2>
                        <ul class="mt-4 space-y-3 text-[15px]">
                            <li><a href="{{ route('about') }}" class="text-white/85 hover:text-white">À propos</a></li>
                            <li><a href="{{ route('case-studies') }}" class="text-white/85 hover:text-white">Réalisations</a></li>
                            <li><a href="{{ route('contact') }}" class="text-white/85 hover:text-white">Contact</a></li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-sm text-white/45">Espace client</h2>
                        <ul class="mt-4 space-y-3 text-[15px]">
                            <li><a href="{{ route('login') }}" class="text-white/85 hover:text-white">Se connecter</a></li>
                            <li><a href="{{ route('register') }}" class="text-white/85 hover:text-white">Créer un compte</a></li>
                            <li><a href="{{ route('requests.create') }}" class="text-white/85 hover:text-white">Déposer une demande</a></li>
                        </ul>
                    </div>
                </div>

                <div class="flex flex-col gap-3 border-t border-white/10 py-6 text-sm text-white/50 sm:flex-row sm:items-center sm:justify-between">
                    <p>© {{ date('Y') }} AgriFish. Tous droits réservés.</p>
                    <div class="flex gap-5">
                        <a href="{{ route('cgv') }}" class="hover:text-white">Conditions de vente</a>
                        <a href="{{ route('mentions-legales') }}" class="hover:text-white">Mentions légales</a>
                        <a href="{{ route('politique-confidentialite') }}" class="hover:text-white">Confidentialité</a>
                    </div>
                </div>
            </div>
        </footer>
    @endif

    @if($contact['whatsapp'] && ! $espace)
        <a href="https://wa.me/{{ preg_replace('/\D/', '', $contact['whatsapp']) }}" target="_blank" rel="noopener noreferrer"
           aria-label="Écrire à AgriFish sur WhatsApp"
           class="fixed bottom-5 right-5 z-40 flex h-12 w-12 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg transition-transform hover:scale-105">
            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.52 3.48A11.79 11.79 0 0 0 12.06 0C5.4 0 .01 5.39 0 12.05c0 2.12.55 4.2 1.6 6.03L0 24l6.08-1.59a11.98 11.98 0 0 0 5.97 1.52h.01c6.66 0 12.05-5.39 12.05-12.05a11.8 11.8 0 0 0-3.59-8.4zM12.06 22.08h-.01a9.97 9.97 0 0 1-5.1-1.4l-.37-.22-3.61.95.96-3.53-.24-.38a9.93 9.93 0 0 1-1.52-5.27C2.17 6.8 6.75 2.22 12.06 2.22a9.8 9.8 0 0 1 6.98 2.89 9.82 9.82 0 0 1 2.88 6.94c0 5.31-4.56 9.99-9.86 9.99zm5.7-7.77c-.31-.15-1.83-.9-2.11-1s-.48-.15-.68.15-.78 1-.96 1.2-.35.23-.66.08a8.12 8.12 0 0 1-2.39-1.47 9 9 0 0 1-1.66-2.06c-.17-.3-.02-.47.13-.62.13-.13.3-.35.44-.53.15-.17.2-.3.3-.5.1-.2.05-.38-.02-.53s-.68-1.66-.94-2.27c-.25-.6-.5-.52-.68-.53h-.58c-.2 0-.53.08-.81.38s-1.06 1.04-1.06 2.53 1.09 2.93 1.24 3.13c.15.2 2.13 3.26 5.16 4.57.72.31 1.28.5 1.71.64.72.23 1.37.2 1.88.12.57-.09 1.83-.75 2.09-1.47.26-.73.26-1.35.18-1.47-.08-.12-.28-.2-.58-.35z"/></svg>
        </a>
    @endif
</body>
</html>
