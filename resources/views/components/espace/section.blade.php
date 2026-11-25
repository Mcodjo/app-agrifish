@props(['title' => null, 'description' => null, 'flush' => false])

<section {{ $attributes->merge(['class' => 'rounded-xl border border-line bg-white']) }}>
    @if($title || isset($actions))
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-4">
            <div>
                @if($title)<h2 class="text-[15px] font-semibold text-dark">{{ $title }}</h2>@endif
                @if($description)<p class="mt-0.5 text-sm text-muted">{{ $description }}</p>@endif
            </div>
            @isset($actions)<div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>@endisset
        </div>
    @endif
    <div class="{{ $flush ? '' : 'p-5' }}">
        {{ $slot }}
    </div>
</section>
