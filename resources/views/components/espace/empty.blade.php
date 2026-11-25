@props(['title', 'text' => null])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-dashed border-line bg-white px-6 py-12 text-center']) }}>
    <p class="text-base font-semibold text-dark">{{ $title }}</p>
    @if($text)<p class="mx-auto mt-1.5 max-w-md text-sm leading-6 text-muted">{{ $text }}</p>@endif
    @if($slot->isNotEmpty())<div class="mt-5 flex flex-wrap justify-center gap-2">{{ $slot }}</div>@endif
</div>
