@extends('layouts.espace')

@section('title', 'Produits')

@section('content')
<x-espace.header title="Produits" subtitle="Catalogue, prix, déclinaisons et stock.">
    <x-slot:actions>
        <a href="{{ route('shop-admin.import-export') }}" class="btn-outline">Import et export</a>
        <a href="{{ route('shop-admin.products.create') }}" class="btn-primary">Nouveau produit</a>
    </x-slot:actions>
</x-espace.header>

<x-espace.body class="space-y-4">
    <x-espace.filters :tabs="['' => 'Tous', 'publies' => 'En ligne', 'brouillons' => 'Brouillons', 'stock-bas' => 'Stock bas', 'rupture' => 'Rupture']" :current="$state" param="etat" :keep="array_filter(['q' => $search, 'categorie' => $category])" />

    <form method="GET" class="flex flex-col gap-3 rounded-xl border border-line bg-white p-4 sm:flex-row">
        @if($state)<input type="hidden" name="etat" value="{{ $state }}">@endif
        <label for="q" class="sr-only">Rechercher</label>
        <input id="q" type="search" name="q" value="{{ $search }}" placeholder="Nom, référence ou SKU" class="field-input !py-2 text-sm sm:flex-1">
        <label for="categorie" class="sr-only">Catégorie</label>
        <select id="categorie" name="categorie" class="field-input !py-2 text-sm sm:w-60">
            <option value="">Toutes les catégories</option>
            @foreach($categories as $parent)
                <option value="{{ $parent->id }}" @selected($category === $parent->id)>{{ $parent->name }}</option>
                @foreach($parent->children as $child)
                    <option value="{{ $child->id }}" @selected($category === $child->id)>— {{ $child->name }}</option>
                @endforeach
            @endforeach
        </select>
        <button type="submit" class="btn-primary !py-2">Filtrer</button>
    </form>

    @if($products->isEmpty())
        <x-espace.empty title="Aucun produit" text="Créez votre premier produit ou importez votre catalogue depuis un fichier Excel.">
            <a href="{{ route('shop-admin.products.create') }}" class="btn-primary">Nouveau produit</a>
            <a href="{{ route('shop-admin.import-export') }}" class="btn-outline">Importer</a>
        </x-espace.empty>
    @else
        <div class="overflow-x-auto rounded-xl border border-line bg-white">
            <table class="w-full min-w-[760px] text-sm">
                <thead class="border-b border-line bg-cream/50 text-left text-xs text-muted">
                    <tr><th class="px-4 py-2.5 font-medium">Produit</th><th class="px-4 py-2.5 font-medium">Catégorie</th><th class="px-4 py-2.5 text-right font-medium">Prix</th><th class="px-4 py-2.5 text-right font-medium">Stock</th><th class="px-4 py-2.5 font-medium">État</th></tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach($products as $product)
                        @php
                            $stock = $product->totalStock();
                            $low = $product->variants->contains(fn ($v) => $v->stock <= $product->low_stock_threshold);
                        @endphp
                        <tr class="hover:bg-cream/40">
                            <td class="px-4 py-3">
                                <a href="{{ route('shop-admin.products.edit', $product) }}" class="flex items-center gap-3">
                                    <span class="h-11 w-11 shrink-0 overflow-hidden rounded-lg bg-cream">@if($product->coverUrl())<img src="{{ $product->coverUrl() }}" alt="" class="h-full w-full object-cover">@endif</span>
                                    <span class="min-w-0">
                                        <span class="block truncate font-medium text-dark hover:underline">{{ $product->name }}</span>
                                        <span class="block text-xs text-muted">{{ $product->reference }} · {{ $product->variants->count() }} {{ $product->variants->count() > 1 ? 'déclinaisons' : 'déclinaison' }}</span>
                                    </span>
                                </a>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $product->category?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right text-dark">{{ $product->hasChoice() ? 'dès ' : '' }}{{ money($product->fromPrice()) }}</td>
                            <td class="px-4 py-3 text-right"><span class="font-medium {{ $stock === 0 ? 'text-red-700' : ($low ? 'text-amber-800' : 'text-dark') }}">{{ $stock }}</span></td>
                            <td class="px-4 py-3">
                                @if($product->trashed())
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Archivé</span>
                                @elseif($product->is_published)
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">En ligne</span>
                                @else
                                    <span class="rounded-full bg-cream px-2.5 py-1 text-xs font-medium text-muted ring-1 ring-inset ring-line">Brouillon</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>{{ $products->links() }}</div>
    @endif
</x-espace.body>
@endsection
