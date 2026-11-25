@extends('layouts.espace')

@php
    $editing = $product->exists;
    $variantRows = old('variants', $editing
        ? $product->variants->map(fn ($v) => ['id' => $v->id, 'name' => $v->name, 'sku' => $v->sku, 'price' => $v->price, 'sale_price' => $v->sale_price, 'stock' => $v->stock])->all()
        : [['id' => null, 'name' => '', 'sku' => '', 'price' => '', 'sale_price' => '', 'stock' => 0]]);
    $date = fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d\TH:i') : '';
@endphp

@section('title', $editing ? $product->name : 'Nouveau produit')

@section('content')
<x-espace.header :back="route('shop-admin.products.index')" back-label="Produits" :title="$editing ? $product->name : 'Nouveau produit'"
                 :subtitle="$editing ? 'Référence '.$product->reference.($product->trashed() ? ' · archivé' : '') : 'Renseignez le produit, ses déclinaisons et ses photos.'">
    @if($editing && $product->is_published && ! $product->trashed())
        <x-slot:actions>
            <a href="{{ route('shop.product', $product) }}" target="_blank" class="btn-outline">Voir dans la boutique</a>
        </x-slot:actions>
    @endif
</x-espace.header>

<x-espace.body class="space-y-6">
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <p class="font-medium">Le produit n’a pas été enregistré :</p>
            <ul class="mt-1 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $editing ? route('shop-admin.products.update', $product) : route('shop-admin.products.store') }}" enctype="multipart/form-data" class="grid items-start gap-6 lg:grid-cols-[1fr_20rem]">
        @csrf
        @if($editing) @method('PUT') @endif

        <div class="space-y-6">
            <x-espace.section title="Informations">
                <div class="grid gap-4 sm:grid-cols-[1fr_12rem]">
                    <div>
                        <label for="name" class="field-label">Nom du produit *</label>
                        <input id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="160" class="field-input">
                    </div>
                    <div>
                        <label for="reference" class="field-label">Référence *</label>
                        <input id="reference" name="reference" value="{{ old('reference', $product->reference) }}" required maxlength="60" class="field-input">
                    </div>
                </div>
                <div class="mt-4">
                    <label for="summary" class="field-label">Résumé</label>
                    <input id="summary" name="summary" value="{{ old('summary', $product->summary) }}" maxlength="300" class="field-input" placeholder="Une phrase affichée sous le nom du produit">
                </div>
                <div class="mt-4">
                    <label for="description" class="field-label">Description</label>
                    <textarea id="description" name="description" rows="7" maxlength="10000" class="field-input">{{ old('description', $product->description) }}</textarea>
                </div>
            </x-espace.section>

            <x-espace.section title="Déclinaisons et prix" description="Un produit compte au moins une déclinaison. Laissez le nom vide s’il n’existe qu’un seul format.">
                <div x-data="{ rows: @js(array_values($variantRows)), add() { this.rows.push({ id: null, name: '', sku: '', price: '', sale_price: '', stock: 0 }) } }">
                    <div class="hidden grid-cols-[1fr_9rem_8rem_8rem_6rem_2rem] gap-2 px-1 pb-2 text-xs font-medium text-muted md:grid">
                        <span>Format</span><span>SKU *</span><span>Prix *</span><span>Prix promo</span><span>Stock</span><span></span>
                    </div>
                    <template x-for="(row, i) in rows" :key="i">
                        <div class="mb-3 grid grid-cols-2 gap-2 rounded-lg border border-line p-3 md:grid-cols-[1fr_9rem_8rem_8rem_6rem_2rem] md:items-center md:border-0 md:p-0" :class="row.remove ? 'opacity-50' : ''">
                            <input type="hidden" :name="`variants[${i}][id]`" :value="row.id ?? ''">
                            <input type="text" :name="`variants[${i}][name]`" x-model="row.name" maxlength="120" placeholder="Ex : Sac de 25 kg" class="field-input !py-2 text-sm col-span-2 md:col-span-1" aria-label="Format">
                            <input type="text" :name="`variants[${i}][sku]`" x-model="row.sku" required maxlength="60" placeholder="SKU" class="field-input !py-2 text-sm" aria-label="SKU">
                            <input type="number" :name="`variants[${i}][price]`" x-model="row.price" required min="0" placeholder="Prix" class="field-input !py-2 text-sm" aria-label="Prix en FCFA">
                            <input type="number" :name="`variants[${i}][sale_price]`" x-model="row.sale_price" min="0" placeholder="Promo" class="field-input !py-2 text-sm" aria-label="Prix promotionnel en FCFA">
                            <template x-if="!row.id">
                                <input type="number" :name="`variants[${i}][stock]`" x-model="row.stock" min="0" class="field-input !py-2 text-sm" aria-label="Stock initial">
                            </template>
                            <template x-if="row.id">
                                <span class="px-2 text-sm text-dark" x-text="row.stock" title="Modifiez le stock dans la section Stock"></span>
                            </template>
                            <label class="flex items-center justify-end" :title="row.id ? 'Supprimer cette déclinaison' : 'Retirer la ligne'">
                                <input type="checkbox" :name="`variants[${i}][remove]`" value="1" x-model="row.remove" class="sr-only">
                                <span class="rounded-md p-1.5 text-muted hover:bg-red-50 hover:text-red-700" :class="row.remove ? 'bg-red-50 text-red-700' : ''" aria-hidden="true">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </span>
                                <span class="sr-only">Supprimer</span>
                            </label>
                        </div>
                    </template>
                    <button type="button" @click="add()" class="btn-outline !py-2 text-sm">+ Ajouter une déclinaison</button>
                    <p class="mt-3 text-xs text-muted">Prix en FCFA TTC. Le prix promotionnel s’applique pendant la période de promotion définie à droite. Le stock des déclinaisons existantes se modifie dans la section Stock, pour garder la trace de chaque mouvement.</p>
                </div>
            </x-espace.section>

            <x-espace.section title="Photos" :description="'Jusqu’à '.config('shop.max_images').' photos, JPG, PNG ou WebP, 4 Mo maximum. Elles sont compressées automatiquement.'">
                @if($editing && $product->images->isNotEmpty())
                    <div class="mb-4 grid grid-cols-3 gap-3 sm:grid-cols-4">
                        @foreach($product->images as $image)
                            <div class="rounded-lg border border-line p-2">
                                <img src="{{ $image->url() }}" alt="" class="aspect-square w-full rounded object-cover">
                                <label class="mt-2 flex items-center gap-1.5 text-xs text-dark"><input type="radio" name="cover_image" value="{{ $image->id }}" @checked($loop->first) class="h-3.5 w-3.5 border-line text-primary-800"> Principale</label>
                                <label class="mt-1 flex items-center gap-1.5 text-xs text-red-700"><input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="h-3.5 w-3.5 rounded border-line text-red-700"> Supprimer</label>
                            </div>
                        @endforeach
                    </div>
                @endif
                <x-file-input name="images[]" id="images" multiple accept="image/jpeg,image/png,image/webp" hint="JPG, PNG ou WebP · 4 Mo max par photo" />
            </x-espace.section>

            <x-espace.section title="Fiche technique et référencement">
                <div class="space-y-4">
                    <div>
                        <span class="field-label">Fiche technique (PDF)</span>
                        @if($product->technical_sheet_path)
                            <p class="mb-2 text-sm"><a href="{{ $product->technicalSheetUrl() }}" target="_blank" class="text-dark underline underline-offset-2">Fiche actuelle</a>
                                <label class="ml-3 inline-flex items-center gap-1.5 text-red-700"><input type="checkbox" name="remove_technical_sheet" value="1" class="h-3.5 w-3.5 rounded"> Retirer</label></p>
                        @endif
                        <input type="file" name="technical_sheet" accept="application/pdf" class="block w-full text-sm text-muted file:mr-3 file:rounded-lg file:border-0 file:bg-cream file:px-3 file:py-2 file:text-sm file:font-medium file:text-dark">
                    </div>
                    <div>
                        <label for="seo_title" class="field-label">Titre pour les moteurs de recherche</label>
                        <input id="seo_title" name="seo_title" value="{{ old('seo_title', $product->seo_title) }}" maxlength="160" class="field-input" placeholder="Par défaut : le nom du produit">
                    </div>
                    <div>
                        <label for="seo_description" class="field-label">Description pour les moteurs de recherche</label>
                        <textarea id="seo_description" name="seo_description" rows="2" maxlength="300" class="field-input" placeholder="Par défaut : le résumé">{{ old('seo_description', $product->seo_description) }}</textarea>
                    </div>
                </div>
            </x-espace.section>
        </div>

        <aside class="space-y-6 lg:sticky lg:top-6">
            <x-espace.section title="Publication">
                <label class="flex items-center justify-between gap-3 text-sm">
                    <span><span class="block font-medium text-dark">En ligne</span><span class="text-muted">Visible dans la boutique</span></span>
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $product->is_published)) class="h-5 w-5 rounded border-line text-primary-800">
                </label>
                <div class="mt-4">
                    <label for="category_id" class="field-label">Catégorie</label>
                    <select id="category_id" name="category_id" class="field-input">
                        <option value="">Sans catégorie</option>
                        @foreach($categories as $parent)
                            <option value="{{ $parent->id }}" @selected((string) old('category_id', $product->category_id) === (string) $parent->id)>{{ $parent->name }}</option>
                            @foreach($parent->children as $child)
                                <option value="{{ $child->id }}" @selected((string) old('category_id', $product->category_id) === (string) $child->id)>— {{ $child->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                    <a href="{{ route('shop-admin.categories.index') }}" class="mt-1 inline-block text-xs text-muted hover:text-dark">Gérer les catégories</a>
                </div>
                <div class="mt-4">
                    <label for="low_stock_threshold" class="field-label">Seuil d’alerte de stock</label>
                    <input id="low_stock_threshold" type="number" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" class="field-input">
                </div>
                <button type="submit" class="btn-primary mt-5 w-full">{{ $editing ? 'Enregistrer' : 'Créer le produit' }}</button>
            </x-espace.section>

            <x-espace.section title="Promotion" description="Pendant cette période, les prix promotionnels remplacent les prix normaux.">
                <div class="space-y-3">
                    <div><label for="sale_starts_at" class="field-label">Début</label><input id="sale_starts_at" type="datetime-local" name="sale_starts_at" value="{{ old('sale_starts_at', $date($product->sale_starts_at)) }}" class="field-input"></div>
                    <div><label for="sale_ends_at" class="field-label">Fin</label><input id="sale_ends_at" type="datetime-local" name="sale_ends_at" value="{{ old('sale_ends_at', $date($product->sale_ends_at)) }}" class="field-input"></div>
                </div>
            </x-espace.section>
        </aside>
    </form>

    @if($editing)
        <div class="grid items-start gap-6 lg:grid-cols-[1fr_20rem]">
            <x-espace.section title="Mouvements de stock" description="Les 30 derniers mouvements, toutes déclinaisons confondues." flush>
                @if($movements->isEmpty())
                    <p class="px-5 py-5 text-sm text-muted">Aucun mouvement pour l’instant.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[560px] text-sm">
                            <thead class="bg-cream/50 text-left text-xs text-muted"><tr><th class="px-5 py-2 font-medium">Date</th><th class="px-5 py-2 font-medium">Déclinaison</th><th class="px-5 py-2 font-medium">Type</th><th class="px-5 py-2 text-right font-medium">Quantité</th><th class="px-5 py-2 text-right font-medium">Stock</th></tr></thead>
                            <tbody class="divide-y divide-line">
                                @foreach($movements as $movement)
                                    <tr>
                                        <td class="px-5 py-2.5 text-muted">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-5 py-2.5 text-dark">{{ $product->variants->firstWhere('id', $movement->product_variant_id)?->label() }}</td>
                                        <td class="px-5 py-2.5">
                                            {{ $movement->typeLabel() }}
                                            @if($movement->order)<a href="{{ route('shop-admin.orders.show', $movement->order) }}" class="text-xs text-muted underline">{{ $movement->order->number }}</a>@endif
                                            @if($movement->note)<span class="block text-xs text-muted">{{ $movement->note }}{{ $movement->user ? ' · '.$movement->user->name : '' }}</span>@endif
                                        </td>
                                        <td class="px-5 py-2.5 text-right font-medium {{ $movement->quantity < 0 ? 'text-red-700' : 'text-primary-800' }}">{{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}</td>
                                        <td class="px-5 py-2.5 text-right">{{ $movement->stock_after }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-espace.section>

            <div class="space-y-6">
                <x-espace.section title="Stock">
                    <ul class="mb-4 space-y-1.5 text-sm">
                        @foreach($product->variants as $variant)
                            <li class="flex justify-between"><span class="text-muted">{{ $variant->label() }}</span><span class="font-medium {{ $variant->stock <= $product->low_stock_threshold ? 'text-amber-800' : 'text-dark' }}">{{ $variant->stock }}</span></li>
                        @endforeach
                    </ul>
                    <form method="POST" action="{{ route('shop-admin.products.stock', $product) }}" class="space-y-3 border-t border-line pt-4">
                        @csrf
                        <div>
                            <label for="variant_id" class="field-label">Déclinaison</label>
                            <select id="variant_id" name="variant_id" class="field-input">
                                @foreach($product->variants as $variant)<option value="{{ $variant->id }}">{{ $variant->label() }}</option>@endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="type" class="field-label">Type</label>
                                <select id="type" name="type" class="field-input">
                                    <option value="restock">Entrée</option>
                                    <option value="adjustment">Correction</option>
                                </select>
                            </div>
                            <div>
                                <label for="quantity" class="field-label">Quantité</label>
                                <input id="quantity" type="number" name="quantity" required class="field-input" placeholder="+10 ou -2">
                            </div>
                        </div>
                        @error('quantity')<p class="field-error">{{ $message }}</p>@enderror
                        <input name="note" maxlength="255" class="field-input" placeholder="Motif (facultatif)" aria-label="Motif">
                        <button type="submit" class="btn-outline w-full">Mettre à jour le stock</button>
                    </form>
                </x-espace.section>

                @unless($product->trashed())
                    <form method="POST" action="{{ route('shop-admin.products.destroy', $product) }}" onsubmit="return confirm('Archiver ce produit ? Il disparaîtra de la boutique, l’historique des ventes est conservé.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn w-full border border-red-200 bg-white text-red-700 hover:bg-red-50">Archiver le produit</button>
                    </form>
                @endunless
            </div>
        </div>
    @endif
</x-espace.body>
@endsection
