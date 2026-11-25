@php
    $pageTitle = trim($__env->yieldContent('title'));
    $fullTitle = ($pageTitle ?: 'Mon espace').' | AgriFish';
    $description = 'Espace AgriFish';
    $noindex = true;
    $user = auth()->user();
    $navigation = \App\Support\EspaceNavigation::for($user);
    $icons = \App\Support\EspaceNavigation::ICONS;
    $initials = collect(explode(' ', $user->name))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    @include('layouts.partials.head')
</head>
<body class="min-h-screen bg-[#f7f6f2]" x-data="{ nav: false }" @keydown.escape.window="nav = false">
    <a href="#contenu" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[80] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:font-semibold focus:shadow-lg">Aller au contenu</a>

    {{-- Barre mobile --}}
    <div class="sticky top-0 z-40 flex h-14 items-center justify-between border-b border-line bg-white px-4 lg:hidden">
        <a href="{{ route('dashboard') }}" aria-label="Mon espace AgriFish"><x-logo /></a>
        <button type="button" @click="nav = true" class="-mr-2 rounded-lg p-2 text-dark" aria-label="Ouvrir le menu" aria-controls="espace-nav" :aria-expanded="nav">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
    </div>

    <div x-show="nav" x-cloak x-transition.opacity class="fixed inset-0 z-50 bg-dark/40 lg:hidden" @click="nav = false"></div>

    {{-- Menu latéral --}}
    <aside id="espace-nav"
           class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-night text-white transition-transform duration-200 lg:z-30 lg:w-64 lg:translate-x-0"
           :class="nav ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" aria-label="Menu de l’espace">
        <div class="flex h-16 items-center justify-between px-5">
            <a href="{{ route('home') }}" aria-label="Site AgriFish"><x-logo inverse /></a>
            <button type="button" @click="nav = false" class="rounded-lg p-1.5 text-white/70 hover:text-white lg:hidden" aria-label="Fermer le menu">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-4">
            @foreach($navigation as $group)
                <div>
                    @if($group['title'])
                        <p class="px-3 pb-2 text-xs font-medium text-white/40">{{ $group['title'] }}</p>
                    @endif
                    <ul class="space-y-0.5">
                        @foreach($group['items'] as $link)
                            <li>
                                <a href="{{ $link['url'] }}" @if($link['active']) aria-current="page" @endif
                                   class="group flex items-center gap-3 rounded-lg px-3 py-2 text-[14.5px] transition-colors {{ $link['active'] ? 'bg-white/10 font-medium text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                                    <svg class="h-[18px] w-[18px] shrink-0 {{ $link['active'] ? 'text-gold-light' : 'text-white/45 group-hover:text-white/70' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $icons[$link['icon']] ?? '' }}"/></svg>
                                    <span class="flex-1 truncate">{{ $link['label'] }}</span>
                                    @if($link['badge'])
                                        <span class="rounded-full bg-gold px-2 py-0.5 text-[11px] font-semibold text-night">{{ $link['badge'] }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        <div class="border-t border-white/10 p-3">
            <div class="flex items-center gap-3 rounded-lg px-2 py-2">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/10 text-sm font-semibold">{{ $initials }}</span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium">{{ $user->name }}</p>
                    <p class="truncate text-xs text-white/50">{{ $user->roleLabel() }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg p-2 text-white/60 hover:bg-white/10 hover:text-white" title="Se déconnecter" aria-label="Se déconnecter">
                        <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-4M10 17l5-5-5-5m5 5H3"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <x-flash />

    <div class="lg:pl-64">
        <main id="contenu" class="min-h-[calc(100vh-3.5rem)] lg:min-h-screen">
            @yield('content')
        </main>
        <footer class="border-t border-line px-5 py-5 text-sm text-muted sm:px-8">
            <div class="mx-auto flex max-w-6xl flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <p>© {{ date('Y') }} AgriFish</p>
                <div class="flex gap-5">
                    <a href="{{ route('contact') }}" class="hover:text-dark">Assistance</a>
                    <a href="{{ route('cgv') }}" class="hover:text-dark">CGV</a>
                    <a href="{{ route('politique-confidentialite') }}" class="hover:text-dark">Confidentialité</a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
