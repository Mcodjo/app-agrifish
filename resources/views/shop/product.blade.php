@extends('layouts.app')

@section('title', $product->seo_title ?: $product->name)
@section('meta_description', $product->seo_description ?: ($product->summary ?: \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 155)))
@section('og_type', 'product')
@if($product->coverUrl())
    @section('og_image', $product->coverUrl())
@endif

@php
    $variants = $product->variants->map(fn ($v) => [
        'id' => $v->id,
        'label' => $v->label(),
        'sku' => $v->sku,
        'price' => money($v->currentPrice()),
        'regular' => $v->isDiscounted() ? money($v->price) : null,
        'stock' => max(0, $v->stock),
    ])->values();
    $firstAvailable = $variants->firstWhere('stock', '>', 0) ?? $variants->first();
    $whatsapp = config('site.contact.whatsapp');
    $images = $product->images;
@endphp

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $product->name,
    'sku' => $product->reference,
    'description' => $product->summary ?: \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 300),
    'image' => $images->map->url()->values(),
    'category' => $product->category?->name,
    'brand' => ['@type' => 'Brand', 'name' => 'AgriFish'],
    'offers' => $product->variants->map(fn ($v) => [
        '@type' => 'Offer',
        'sku' => $v->sku,
        'price' => $v->currentPrice(),
        'priceCurrency' => 'XOF',
        'availability' => $v->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        'url' => route('shop.product', $product),
    ])->values(),
] + ($rating ? ['aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => $rating, 'reviewCount' => $product->approvedReviews->count()]] : []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}
</script>
@endpush

@section('content')
<div class="max-w-content px-site pt-6">
    <nav class="flex flex-wrap items-center gap-1.5 text-sm text-muted" aria-label="Fil d’Ariane">
        <a href="{{ route('shop.index') }}" class="hover:text-dark">Boutique</a>
        @if($product->category)
            @if($product->category->parent)
                <span aria-hidden="true">/</span>
                <a href="{{ route('shop.index', ['categorie' => $product->category->parent->slug]) }}" class="hover:text-dark">{{ $product->category->parent->name }}</a>
            @endif
            <span aria-hidden="true">/</span>
            <a href="{{ route('shop.index', ['categorie' => $product->category->slug]) }}" class="hover:text-dark">{{ $product->category->name }}</a>
        @endif
    </nav>
</div>

<section class="max-w-content px-site py-8 lg:py-10">
    <div class="grid gap-10 lg:grid-cols-2 lg:gap-14">
        <div x-data="{ current: 0 }">
            <div class="aspect-[4/3] overflow-hidden rounded-2xl bg-cream">
                @forelse($images as $image)
                    <img x-show="current === {{ $loop->index }}" @if(! $loop->first) x-cloak @endif src="{{ $image->url() }}" alt="{{ $product->name }}{{ $images->count() > 1 ? ', photo '.$loop->iteration : '' }}" class="h-full w-full object-cover" @if(! $loop->first) loading="lazy" @endif>
                @empty
                    <div class="flex h-full items-center justify-center text-muted">Photo à venir</div>
                @endforelse
            </div>
            @if($images->count() > 1)
                <div class="mt-3 grid grid-cols-5 gap-2 sm:grid-cols-6">
                    @foreach($images as $image)
                        <button type="button" @click="current = {{ $loop->index }}" class="aspect-square overflow-hidden rounded-lg ring-2 ring-offset-1 transition" :class="current === {{ $loop->index }} ? 'ring-primary-800' : 'ring-transparent opacity-80 hover:opacity-100'" aria-label="Voir la photo {{ $loop->iteration }}">
                            <img src="{{ $image->url() }}" alt="" class="h-full w-full object-cover" loading="lazy">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div x-data="{ variants: @js($variants), selected: @js($firstAvailable['id'] ?? null), qty: 1,
                       get v() { return this.variants.find(x => x.id === this.selected) || this.variants[0] } }">
            @if($product->category)
                <p class="text-sm text-muted">{{ $product->category->fullName() }}</p>
            @endif
            <h1 class="mt-1 font-serif text-[2rem] leading-tight text-dark sm:text-[2.4rem]">{{ $product->name }}</h1>
            @if($rating)
                <a href="#avis" class="mt-2 inline-flex items-center gap-2 text-sm text-muted hover:text-dark">
                    <x-shop.stars :value="$rating" />
                    {{ number_format($rating, 1, ',', '') }} · {{ $product->approvedReviews->count() }} avis
                </a>
            @endif
            @if($product->summary)
                <p class="mt-4 text-[17px] leading-8 text-muted">{{ $product->summary }}</p>
            @endif

            <div class="mt-6 flex items-baseline gap-3">
                <span class="text-3xl font-semibold text-dark" x-text="v.price">{{ $firstAvailable['price'] ?? '' }}</span>
                <span class="text-lg text-muted line-through" x-show="v.regular" x-text="v.regular" x-cloak></span>
            </div>
            <p class="mt-1 text-sm text-muted">Prix TTC · Référence <span x-text="v.sku">{{ $firstAvailable['sku'] ?? '' }}</span></p>

            <form method="POST" action="{{ route('shop.cart.add') }}" class="mt-6 space-y-5">
                @csrf
                <input type="hidden" name="variant_id" :value="selected">

                @if($product->hasChoice())
                    <fieldset>
                        <legend class="field-label">Format</legend>
                        <div class="mt-1 flex flex-wrap gap-2">
                            <template x-for="item in variants" :key="item.id">
                                <label class="cursor-pointer">
                                    <input type="radio" class="peer sr-only" name="_variant" :value="item.id" x-model.number="selected">
                                    <span class="block rounded-lg border px-4 py-2 text-sm transition peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500"
                                          :class="selected === item.id ? 'border-primary-800 bg-primary-50 font-medium text-primary-800' : (item.stock > 0 ? 'border-line text-dark hover:border-dark/30' : 'border-line text-muted line-through')"
                                          x-text="item.label"></span>
                                </label>
                            </template>
                        </div>
                    </fieldset>
                @endif

                <p class="text-sm" :class="v.stock > 0 ? 'text-primary-800' : 'text-red-700'">
                    <span x-show="v.stock > 5">En stock</span>
                    <span x-show="v.stock > 0 && v.stock <= 5" x-cloak>Plus que <span x-text="v.stock"></span> en stock</span>
                    <span x-show="v.stock === 0" x-cloak>Rupture de stock</span>
                </p>

                @error('quantity')<p class="field-error">{{ $message }}</p>@enderror

                <div class="flex flex-wrap items-end gap-3" x-show="v.stock > 0">
                    <div>
                        <label for="qty" class="field-label">Quantité</label>
                        <div class="flex items-center rounded-[10px] border border-line">
                            <button type="button" @click="qty = Math.max(1, qty - 1)" class="px-3 py-2.5 text-dark" aria-label="Diminuer">−</button>
                            <input id="qty" name="quantity" type="number" min="1" :max="v.stock" x-model.number="qty" class="w-14 border-0 p-0 text-center focus:ring-0">
                            <button type="button" @click="qty = Math.min(v.stock, qty + 1)" class="px-3 py-2.5 text-dark" aria-label="Augmenter">+</button>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary flex-1 sm:flex-none">Ajouter au panier</button>
                    <button type="submit" name="buy_now" value="1" class="btn-secondary flex-1 sm:flex-none">Commander maintenant</button>
                </div>
            </form>

            <div class="mt-6 flex flex-wrap gap-3">
                @if($whatsapp)
                    <a :href="'https://wa.me/{{ preg_replace('/\D/', '', $whatsapp) }}?text=' + encodeURIComponent('Bonjour, je souhaite commander : {{ addslashes($product->name) }} (' + v.label + ', réf. ' + v.sku + '). {{ route('shop.product', $product) }}')"
                       target="_blank" rel="noopener noreferrer" class="btn-outline">
                        <svg class="h-4 w-4 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.06 0C5.4 0 .01 5.39 0 12.05c0 2.12.55 4.2 1.6 6.03L0 24l6.08-1.59a11.98 11.98 0 0 0 5.97 1.52h.01c6.66 0 12.05-5.39 12.05-12.05S18.72 0 12.06 0z"/></svg>
                        Commander sur WhatsApp
                    </a>
                @endif
                @if($product->technicalSheetUrl())
                    <a href="{{ $product->technicalSheetUrl() }}" target="_blank" rel="noopener" class="btn-outline">Fiche technique (PDF)</a>
                @endif
            </div>

            <dl class="mt-8 grid gap-3 rounded-xl bg-cream p-4 text-sm sm:grid-cols-2">
                <div><dt class="font-medium text-dark">Paiement</dt><dd class="text-muted">Mobile Money, carte bancaire ou à la remise</dd></div>
                <div><dt class="font-medium text-dark">Remise de la commande</dt><dd class="text-muted">Organisée avec vous par notre équipe</dd></div>
            </dl>
        </div>
    </div>

    @if($product->description)
        <div class="mt-14 grid gap-8 border-t border-line pt-10 lg:grid-cols-[15rem_1fr]">
            <h2 class="text-lg font-semibold text-dark">Description</h2>
            <div class="max-w-3xl whitespace-pre-line text-[16px] leading-8 text-muted">{{ $product->description }}</div>
        </div>
    @endif

    <div id="avis" class="mt-14 grid scroll-mt-24 gap-8 border-t border-line pt-10 lg:grid-cols-[15rem_1fr]">
        <div>
            <h2 class="text-lg font-semibold text-dark">Avis clients</h2>
            @if($rating)
                <p class="mt-2 flex items-center gap-2 text-sm text-muted"><x-shop.stars :value="$rating" /> {{ number_format($rating, 1, ',', '') }} sur 5</p>
            @endif
        </div>
        <div class="max-w-3xl space-y-6">
            @forelse($product->approvedReviews as $review)
                <article class="border-b border-line pb-6 last:border-0">
                    <div class="flex items-center gap-3">
                        <x-shop.stars :value="$review->rating" />
                        <p class="text-sm font-medium text-dark">{{ \Illuminate\Support\Str::of($review->user?->name ?? 'Client')->explode(' ')->first() }}</p>
                        <time class="text-sm text-muted" datetime="{{ $review->created_at->toDateString() }}">{{ $review->created_at->translatedFormat('d F Y') }}</time>
                    </div>
                    @if($review->comment)
                        <p class="mt-2 whitespace-pre-line text-[15px] leading-7 text-muted">{{ $review->comment }}</p>
                    @endif
                </article>
            @empty
                <p class="text-muted">Pas encore d’avis. Les clients qui ont reçu ce produit peuvent le noter depuis leur espace.</p>
            @endforelse
        </div>
    </div>

    @if($related->isNotEmpty())
        <div class="mt-14 border-t border-line pt-10">
            <h2 class="text-lg font-semibold text-dark">Dans la même catégorie</h2>
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($related as $item)
                    <x-shop.product-card :product="$item" />
                @endforeach
            </div>
        </div>
    @endif
</section>
@endsection
