@props(['eyebrow' => null, 'title', 'subtitle' => null, 'back' => null, 'backLabel' => 'Retour'])

<section class="border-b border-line bg-cream/60">
    <div class="max-w-content px-site py-8 lg:py-10">
        @if($back)
            <a href="{{ $back }}" class="mb-5 inline-flex items-center gap-1.5 text-sm text-muted hover:text-dark">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                {{ $backLabel }}
            </a>
        @endif
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="min-w-0">
                @if($eyebrow)
                    <p class="text-sm text-muted">{{ $eyebrow }}</p>
                @endif
                <h1 class="mt-1 text-[1.75rem] font-semibold leading-tight tracking-[-0.01em] text-dark sm:text-3xl">{{ $title }}</h1>
                @if($subtitle)
                    <p class="mt-2 max-w-2xl text-[15px] leading-7 text-muted">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex shrink-0 flex-wrap gap-2">{{ $actions }}</div>
            @endisset
        </div>
        {{ $slot }}
    </div>
</section>
