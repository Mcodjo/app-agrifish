@props(['title', 'intro' => null, 'label' => null])

<section class="border-b border-line bg-white">
    <div class="max-w-content px-site pb-14 pt-14 sm:pt-20 lg:pb-20">
        @if($label)
            <p class="mb-5 text-sm font-medium text-muted">{{ $label }}</p>
        @endif
        <div class="grid gap-8 lg:grid-cols-[1.25fr_1fr] lg:items-end">
            <h1 class="display-lg max-w-3xl">{{ $title }}</h1>
            @if($intro)
                <p class="lead max-w-xl lg:pb-2">{{ $intro }}</p>
            @endif
        </div>
        {{ $slot }}
    </div>
</section>
