@props([
    'title'   => 'Prêt à démarrer ?',
    'subtitle' => null,
    'btnLabel' => 'Nous contacter',
    'btnRoute' => 'contact',
    'btn2Label' => null,
    'btn2Route' => null,
])

<section class="py-20 bg-primary-dark">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-extrabold text-white mb-5">{{ $title }}</h2>
        @if($subtitle)
            <p class="text-white/70 mb-8">{{ $subtitle }}</p>
        @endif
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route($btnRoute) }}"
               class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gold text-white font-bold rounded-xl text-lg hover:bg-gold-dark transition-colors">
                {{ $btnLabel }}
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
            @if($btn2Label && $btn2Route)
                <a href="{{ route($btn2Route) }}"
                   class="inline-flex items-center justify-center gap-2 px-8 py-4 border-2 border-white/40 text-white font-semibold rounded-xl text-lg hover:bg-white/10 transition-colors">
                    {{ $btn2Label }}
                </a>
            @endif
        </div>
    </div>
</section>
