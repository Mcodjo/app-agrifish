@extends('layouts.espace')

@section('title', 'Import et export')

@php $result = session('import_result'); @endphp

@section('content')
<x-espace.header title="Import et export" subtitle="Gérez le catalogue depuis Excel et exportez les ventes pour la comptabilité." />

<x-espace.body>
    @if($result)
        <div class="mb-6 rounded-xl border {{ $result['errors'] ? 'border-amber-200 bg-amber-50' : 'border-primary-200 bg-primary-50' }} px-4 py-3 text-sm text-dark" role="status">
            <p class="font-medium">Import terminé : {{ $result['created'] }} produit(s) créé(s), {{ $result['updated'] }} mis à jour.</p>
            @if($result['errors'])
                <p class="mt-1">{{ count($result['errors']) }} ligne(s) ignorée(s) :</p>
                <ul class="mt-1 list-disc pl-5">@foreach($result['errors'] as $line => $error)<li>Ligne {{ $line }} : {{ $error }}</li>@endforeach</ul>
            @endif
        </div>
    @endif

    <div class="grid items-start gap-6 lg:grid-cols-2">
        <x-espace.section title="Exporter">
            <p class="text-sm leading-6 text-muted">Le catalogue exporté sert aussi de modèle pour l’import : modifiez-le puis réimportez-le.</p>
            <a href="{{ route('shop-admin.export.catalog') }}" class="btn-outline mt-4">Télécharger le catalogue (.xlsx)</a>

            <form method="GET" action="{{ route('shop-admin.export.sales') }}" class="mt-6 space-y-3 border-t border-line pt-5">
                <h3 class="font-medium text-dark">Ventes</h3>
                <p class="text-sm text-muted">Une ligne par article vendu, avec le client, le paiement et les montants.</p>
                <div class="grid grid-cols-2 gap-3">
                    <div><label for="du" class="field-label">Du</label><input id="du" type="date" name="du" value="{{ now()->startOfMonth()->format('Y-m-d') }}" class="field-input"></div>
                    <div><label for="au" class="field-label">Au</label><input id="au" type="date" name="au" value="{{ now()->format('Y-m-d') }}" class="field-input"></div>
                </div>
                <button type="submit" class="btn-outline">Télécharger les ventes (.xlsx)</button>
            </form>
        </x-espace.section>

        <x-espace.section title="Importer le catalogue">
            <p class="text-sm leading-6 text-muted">Fichier Excel (.xlsx) ou CSV, une ligne par déclinaison. Les produits sont reconnus par leur référence et les déclinaisons par leur SKU : une ligne existante est mise à jour, une nouvelle est créée. Le stock indiqué remplace le stock actuel et le mouvement est tracé.</p>
            <p class="mt-3 text-sm text-dark">Colonnes attendues :</p>
            <p class="mt-1 font-mono text-xs leading-6 text-muted">{{ implode(' · ', $columns) }}</p>
            <form method="POST" action="{{ route('shop-admin.import.catalog') }}" enctype="multipart/form-data" class="mt-5 space-y-3">
                @csrf
                <input type="file" name="file" accept=".xlsx,.csv" required class="block w-full text-sm text-muted file:mr-3 file:rounded-lg file:border-0 file:bg-cream file:px-3 file:py-2 file:text-sm file:font-medium file:text-dark">
                @error('file')<p class="field-error">{{ $message }}</p>@enderror
                <button type="submit" class="btn-primary">Importer</button>
            </form>
        </x-espace.section>
    </div>
</x-espace.body>
@endsection
