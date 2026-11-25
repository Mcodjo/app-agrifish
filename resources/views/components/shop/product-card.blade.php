@props(['product'])

@php
    $cover = $product->coverUrl();
    $price = $product->fromPrice();
    $regular = $product->fromRegularPrice();
    $available = $product->isAvailable();
@endphp
<a href="{{ route('shop.product', $product) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-line bg-white transition hover:border-dark/25">
    <div class="relative aspect-[4/3] overflow-hidden bg-cream">
        @if($cover)
            <img src="{{ $cover }}" alt="{{ $product->name }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]">
        @else
            <div class="flex h-full items-center justify-center text-sm text-muted">Photo à venir</div>
        @endif
        @if(! $available)
            <span class="absolute left-3 top-3 rounded-full bg-white/95 px-2.5 py-1 text-xs font-medium text-dark">Rupture de stock</span>
        @elseif($price < $regular)
            <span class="absolute left-3 top-3 rounded-full bg-gold px-2.5 py-1 text-xs font-semibold text-night">Promotion</span>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-4">
        @if($product->category)
            <p class="text-xs text-muted">{{ $product->category->name }}</p>
        @endif
        <h3 class="mt-1 font-semibold leading-snug text-dark group-hover:underline">{{ $product->name }}</h3>
        @if($product->summary)
            <p class="mt-1 line-clamp-2 text-sm leading-6 text-muted">{{ $product->summary }}</p>
        @endif
        <div class="mt-auto flex items-baseline gap-2 pt-3">
            <span class="text-[15px] font-semibold text-dark">{{ $product->hasChoice() ? 'À partir de ' : '' }}{{ money($price) }}</span>
            @if($price < $regular)
                <span class="text-sm text-muted line-through">{{ money($regular) }}</span>
            @endif
        </div>
    </div>
</a>
