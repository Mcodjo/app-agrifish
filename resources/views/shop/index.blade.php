@extends('layouts.app')

@section('title', $currentCategory ? $currentCategory->name.' · Boutique' : 'Boutique')
@section('meta_description', 'Boutique AgriFish : intrants, matériel et produits pour l’agriculture et l’aquaculture, avec paiement par Mobile Money ou carte bancaire.')

@php
    $keep = array_filter(['categorie' => request('categorie'), 'q' => $search, 'tri' => $sort !== 'nouveautes' ? $sort : null, 'min' => $min, 'max' => $max, 'disponible' => $available ? 1 : null], fn ($v) => $v !== null && $v !== '');
@endphp

@section('content')
<section class="border-b border-line bg-cream/60">
    <div class="max-w-content px-site py-10 lg:py-12">
        <p class="text-sm text-muted">Boutique</p>
        <h1 class="display-lg mt-2">{{ $currentCategory?->name ?? 'Nos produits' }}</h1>
        <p class="lead mt-3 max-w-2xl">{{ $currentCategory?->description ?: 'Intrants, matériel et produits sélectionnés par nos équipes. Paiement par Mobile Money, carte bancaire ou à la remise.' }}</p>
    </div>
</section>

<section class="max-w-content px-site py-10 lg:py-12" x-data="{ filters: false }">
    <div class="grid gap-8 lg:grid-cols-[15rem_1fr]">
        <aside>
            <button type="button" @click="filters = !filters" class="btn-outline w-full lg:hidden" :aria-expanded="filters">Catégories et filtres</button>
            <div class="mt-4 space-y-8 lg:mt-0 lg:block" :class="filters ? 'block' : 'hidden'">
                <nav aria-label="Catégories">
                    <h2 class="text-sm font-semibold text-dark">Catégories</h2>
                    <ul class="mt-3 space-y-1 text-[15px]">
                        <li><a href="{{ route('shop.index', \Illuminate\Support\Arr::except($keep, ['categorie'])) }}" class="block rounded-lg px-2.5 py-1.5 {{ ! $currentCategory ? 'bg-cream font-medium text-dark' : 'text-muted hover:text-dark' }}">Tous les produits</a></li>
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('shop.index', array_merge($keep, ['categorie' => $category->slug])) }}" class="block rounded-lg px-2.5 py-1.5 {{ $currentCategory?->id === $category->id ? 'bg-cream font-medium text-dark' : 'text-muted hover:text-dark' }}">{{ $category->name }}</a>
                                @if($category->children->isNotEmpty())
                                    <ul class="ml-3 mt-1 space-y-1 border-l border-line pl-2 text-sm">
                                        @foreach($category->children as $child)
                                            <li><a href="{{ route('shop.index', array_merge($keep, ['categorie' => $child->slug])) }}" class="block rounded-lg px-2.5 py-1 {{ $currentCategory?->id === $child->id ? 'bg-cream font-medium text-dark' : 'text-muted hover:text-dark' }}">{{ $child->name }}</a></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </nav>

                <form method="GET" action="{{ route('shop.index') }}" class="space-y-4">
                    @foreach(\Illuminate\Support\Arr::only($keep, ['categorie', 'q', 'tri']) as $name => $value)
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                    @endforeach
                    <h2 class="text-sm font-semibold text-dark">Prix (FCFA)</h2>
                    <div class="flex items-center gap-2">
                        <label class="sr-only" for="min">Prix minimum</label>
                        <input id="min" type="number" name="min" min="0" step="500" value="{{ $min }}" placeholder="Min" class="field-input !py-2 text-sm">
                        <span class="text-muted">–</span>
                        <label class="sr-only" for="max">Prix maximum</label>
                        <input id="max" type="number" name="max" min="0" step="500" value="{{ $max }}" placeholder="Max" class="field-input !py-2 text-sm">
                    </div>
                    <label class="flex items-center gap-2.5 text-sm text-dark">
                        <input type="checkbox" name="disponible" value="1" @checked($available) class="h-4 w-4 rounded border-line text-primary-800 focus:ring-primary-500">
                        En stock uniquement
                    </label>
                    <button type="submit" class="btn-secondary w-full !py-2">Appliquer</button>
                </form>
            </div>
        </aside>

        <div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <form method="GET" action="{{ route('shop.index') }}" class="relative flex-1 sm:max-w-sm">
                    @foreach(\Illuminate\Support\Arr::except($keep, ['q']) as $name => $value)
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                    @endforeach
                    <label for="shop-search" class="sr-only">Rechercher un produit</label>
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="shop-search" type="search" name="q" value="{{ $search }}" placeholder="Nom ou référence" class="field-input !py-2.5 pl-9">
                </form>
                <div class="flex items-center gap-3">
                    <p class="text-sm text-muted">{{ $products->total() }} {{ $products->total() > 1 ? 'produits' : 'produit' }}</p>
                    <form method="GET" action="{{ route('shop.index') }}">
                        @foreach(\Illuminate\Support\Arr::except($keep, ['tri']) as $name => $value)
                            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                        @endforeach
                        <label for="tri" class="sr-only">Trier</label>
                        <select id="tri" name="tri" onchange="this.form.submit()" class="field-input !py-2 text-sm">
                            @foreach($sorts as $value => $label)
                                <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            @if($products->isEmpty())
                <div class="mt-6 rounded-2xl border border-dashed border-line px-6 py-16 text-center">
                    <p class="text-lg font-semibold text-dark">Aucun produit ne correspond</p>
                    <p class="mt-2 text-muted">Modifiez la recherche ou les filtres.</p>
                    <a href="{{ route('shop.index') }}" class="btn-outline mt-6">Voir tous les produits</a>
                </div>
            @else
                <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($products as $product)
                        <x-shop.product-card :product="$product" />
                    @endforeach
                </div>
                <div class="mt-8">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</section>
@endsection
