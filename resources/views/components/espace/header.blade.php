@props(['title', 'subtitle' => null, 'back' => null, 'backLabel' => 'Retour', 'eyebrow' => null])

<header class="border-b border-line bg-white">
    <div class="mx-auto max-w-6xl px-5 py-6 sm:px-8 lg:py-7">
        @if($back)
            <a href="{{ $back }}" class="mb-3 inline-flex items-center gap-1 text-sm text-muted hover:text-dark">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                {{ $backLabel }}
            </a>
        @endif
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="min-w-0">
                @if($eyebrow)
                    <p class="text-sm text-muted">{{ $eyebrow }}</p>
                @endif
                <h1 class="text-2xl font-semibold tracking-[-0.01em] text-dark sm:text-[1.75rem]">{{ $title }}</h1>
                @if($subtitle)
                    <p class="mt-1.5 max-w-2xl text-[15px] leading-7 text-muted">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
        {{ $slot }}
    </div>
</header>
