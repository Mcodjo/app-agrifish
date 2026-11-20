@extends('layouts.app')

@section('title', 'Signataires · '.config('petition.title'))

@section('content')
<header class="hero">
    <p class="eyebrow">{{ number_format($count, 0, ',', ' ') }} {{ $count > 1 ? 'signatures' : 'signature' }}</p>
    <h1>Les signataires</h1>
    <p class="lede">Toutes les personnes qui demandent que les événements tech et de l’innovation viennent aussi à Porto-Novo.</p>
</header>

<div class="list">
    @forelse ($signatures as $s)
        @include('petition._signature', ['s' => $s])
    @empty
        <div class="empty">Aucune signature pour l’instant. <a href="{{ route('petition.index') }}#signer">Signez la pétition</a>.</div>
    @endforelse
</div>

@if ($signatures->hasPages())
    <nav class="pagination" aria-label="Pages">
        @if ($signatures->previousPageUrl())<a href="{{ $signatures->previousPageUrl() }}">← Précédent</a>@endif
        <span class="active">Page {{ $signatures->currentPage() }} / {{ $signatures->lastPage() }}</span>
        @if ($signatures->nextPageUrl())<a href="{{ $signatures->nextPageUrl() }}">Suivant →</a>@endif
    </nav>
@endif

<p style="margin-top: 28px"><a class="btn" href="{{ route('petition.index') }}#signer">Signer à mon tour</a></p>
@endsection
