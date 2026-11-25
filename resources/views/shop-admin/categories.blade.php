@extends('layouts.espace')

@section('title', 'Catégories')

@section('content')
<x-espace.header title="Catégories" subtitle="Les catégories s’organisent sur deux niveaux, par exemple Aquaculture › Aliments poisson." />

<x-espace.body>
    @error('category')<div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</div>@enderror

    <div class="grid items-start gap-6 lg:grid-cols-[1fr_20rem]">
        <div class="space-y-3">
            @forelse($categories as $parent)
                <div class="overflow-hidden rounded-xl border border-line bg-white">
                    @foreach(collect([$parent])->merge($parent->children) as $category)
                        <div x-data="{ edit: false }" class="{{ $category->parent_id ? 'border-t border-line bg-cream/30 pl-8' : '' }}">
                            <div class="flex items-center justify-between gap-3 px-5 py-3">
                                <div class="min-w-0">
                                    <p class="truncate {{ $category->parent_id ? 'text-dark' : 'font-semibold text-dark' }}">{{ $category->name }}</p>
                                    <p class="text-xs text-muted">{{ $category->products_count }} {{ $category->products_count > 1 ? 'produits' : 'produit' }}</p>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <a href="{{ route('shop.index', ['categorie' => $category->slug]) }}" target="_blank" class="text-muted hover:text-dark">Voir</a>
                                    <button type="button" @click="edit = !edit" class="font-medium text-primary-800" x-text="edit ? 'Fermer' : 'Modifier'">Modifier</button>
                                    <form method="POST" action="{{ route('shop-admin.categories.destroy', $category) }}" onsubmit="return confirm('Supprimer cette catégorie ?')">@csrf @method('DELETE')<button class="text-red-700 hover:underline">Supprimer</button></form>
                                </div>
                            </div>
                            <form x-show="edit" x-cloak method="POST" action="{{ route('shop-admin.categories.update', $category) }}" class="grid gap-3 border-t border-line bg-cream px-5 py-4 sm:grid-cols-2">
                                @csrf @method('PATCH')
                                <div><label class="field-label" for="name-{{ $category->id }}">Nom</label><input id="name-{{ $category->id }}" name="name" value="{{ $category->name }}" required class="field-input"></div>
                                <div>
                                    <label class="field-label" for="parent-{{ $category->id }}">Catégorie parente</label>
                                    <select id="parent-{{ $category->id }}" name="parent_id" class="field-input">
                                        <option value="">Aucune (premier niveau)</option>
                                        @foreach($categories as $option)
                                            @if($option->id !== $category->id)<option value="{{ $option->id }}" @selected($category->parent_id === $option->id)>{{ $option->name }}</option>@endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="sm:col-span-2"><label class="field-label" for="desc-{{ $category->id }}">Description (affichée en tête de la catégorie)</label><textarea id="desc-{{ $category->id }}" name="description" rows="2" class="field-input">{{ $category->description }}</textarea></div>
                                <div><label class="field-label" for="order-{{ $category->id }}">Ordre d’affichage</label><input id="order-{{ $category->id }}" type="number" name="sort_order" min="0" value="{{ $category->sort_order }}" class="field-input"></div>
                                <div class="flex items-end"><button type="submit" class="btn-primary">Enregistrer</button></div>
                            </form>
                        </div>
                    @endforeach
                </div>
            @empty
                <x-espace.empty title="Aucune catégorie" text="Créez vos premières catégories pour organiser la boutique." />
            @endforelse
        </div>

        <x-espace.section title="Nouvelle catégorie">
            <form method="POST" action="{{ route('shop-admin.categories.store') }}" class="space-y-3">
                @csrf
                <div><label for="name" class="field-label">Nom *</label><input id="name" name="name" value="{{ old('name') }}" required maxlength="120" class="field-input">@error('name')<p class="field-error">{{ $message }}</p>@enderror</div>
                <div>
                    <label for="parent_id" class="field-label">Catégorie parente</label>
                    <select id="parent_id" name="parent_id" class="field-input">
                        <option value="">Aucune (premier niveau)</option>
                        @foreach($categories as $option)<option value="{{ $option->id }}">{{ $option->name }}</option>@endforeach
                    </select>
                    @error('parent_id')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div><label for="description" class="field-label">Description</label><textarea id="description" name="description" rows="2" class="field-input">{{ old('description') }}</textarea></div>
                <button type="submit" class="btn-primary w-full">Créer</button>
            </form>
        </x-espace.section>
    </div>
</x-espace.body>
@endsection
