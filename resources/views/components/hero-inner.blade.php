@props([
    'badge'    => null,
    'title'    => '',
    'subtitle' => null,
])

<section class="hero-gradient pt-32 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        @if($badge)
            <span class="inline-block px-4 py-1.5 bg-white/10 text-gold-light text-sm font-medium rounded-full border border-gold/30 mb-4">
                {{ $badge }}
            </span>
        @endif
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-5">{!! $title !!}</h1>
        @if($subtitle)
            <p class="text-white/75 text-lg max-w-2xl mx-auto">{{ $subtitle }}</p>
        @endif
        {{ $slot ?? '' }}
    </div>
</section>
