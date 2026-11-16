@props(['eyebrow' => null, 'title', 'subtitle' => null, 'back' => null, 'backLabel' => 'Retour'])

{{-- En-tête compact pour les écrans de l'espace connecté (au lieu d'un grand hero marketing). --}}
<section class="relative overflow-hidden bg-primary-dark text-white">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
    <div class="relative z-10 max-w-content px-4 py-10 lg:px-8 lg:py-12">
        @if($back)
            <a href="{{ $back }}" class="mb-5 inline-flex items-center gap-1.5 text-sm font-medium text-white/70 transition hover:text-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                {{ $backLabel }}
            </a>
        @endif
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="min-w-0">
                @if($eyebrow)
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-gold-light">{{ $eyebrow }}</p>
                @endif
                <h1 class="mt-2 text-3xl font-extrabold leading-tight sm:text-4xl">{{ $title }}</h1>
                @if($subtitle)
                    <p class="mt-3 max-w-2xl text-base leading-7 text-white/75">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex shrink-0 flex-wrap gap-3">{{ $actions }}</div>
            @endisset
        </div>
        {{ $slot }}
    </div>
</section>
