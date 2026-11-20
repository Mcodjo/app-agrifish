@extends('layouts.app')

@section('title', 'Administration · '.config('petition.title'))

@push('head')<meta name="robots" content="noindex">@endpush

@section('content')
<header class="hero" style="padding-bottom: 0">
    <p class="eyebrow">Administration</p>
    <h1>Signatures</h1>
</header>

@if (session('status'))
    <div class="alert ok" style="margin-top: 14px">{{ session('status') }}</div>
@endif

<div class="stats">
    <div class="card"><b>{{ $stats['verified'] }}</b><span>confirmées</span></div>
    <div class="card"><b>{{ $stats['pending'] }}</b><span>en attente de confirmation</span></div>
    <div class="card"><b>{{ $stats['today'] }}</b><span>aujourd’hui</span></div>
    <div class="card"><b>{{ $stats['total'] }}</b><span>au total</span></div>
</div>

<div class="cols">
    <section class="card mini">
        <h3>Par profil</h3>
        <ol>@forelse ($profiles as $p)<li>{{ config('petition.profiles')[$p->profile] ?? $p->profile }} <span>{{ $p->total }}</span></li>@empty<li>Aucune donnée</li>@endforelse</ol>
    </section>
    <section class="card mini">
        <h3>Par commune</h3>
        <ol>@forelse ($cities as $c)<li>{{ $c->city }} <span>{{ $c->total }}</span></li>@empty<li>Aucune donnée</li>@endforelse</ol>
    </section>
</div>

<form class="toolbar" method="GET" action="{{ route('admin.index') }}">
    <label class="sr-only" for="q">Rechercher</label>
    <input type="search" id="q" name="q" value="{{ $q }}" placeholder="Nom, e-mail, commune…">
    <label class="sr-only" for="status">Statut</label>
    <select id="status" name="status">
        <option value="">Tous les statuts</option>
        <option value="verified" @selected($status === 'verified')>Confirmées</option>
        <option value="pending" @selected($status === 'pending')>En attente</option>
    </select>
    <button class="btn small" type="submit">Filtrer</button>
    <a class="btn ghost small" href="{{ route('admin.export') }}">Exporter en CSV</a>
</form>

<div class="table-wrap">
    <table>
        <thead><tr><th>Signataire</th><th>E-mail</th><th>Commune</th><th>Profil</th><th>Commentaire</th><th>Statut</th><th>Date</th><th></th></tr></thead>
        <tbody>
        @forelse ($signatures as $s)
            <tr>
                <td>{{ $s->first_name }} {{ $s->last_name }}@unless($s->show_name)<br><small style="color: var(--muted)">nom masqué</small>@endunless</td>
                <td>{{ $s->email }}</td>
                <td>{{ $s->city }}</td>
                <td>{{ $s->profileLabel() }}</td>
                <td style="max-width: 260px">{{ $s->comment }}</td>
                <td>@if ($s->verified_at)<span class="badge ok">Confirmée</span>@else<span class="badge wait">En attente</span>@endif</td>
                <td style="white-space: nowrap">{{ $s->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.destroy', $s) }}" onsubmit="return confirm('Supprimer cette signature ?')">
                        @csrf @method('DELETE')
                        <button class="btn danger small" type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" style="text-align: center; color: var(--muted); padding: 24px">Aucune signature trouvée.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@if ($signatures->hasPages())
    <nav class="pagination" aria-label="Pages">
        @if ($signatures->previousPageUrl())<a href="{{ $signatures->previousPageUrl() }}">← Précédent</a>@endif
        <span class="active">Page {{ $signatures->currentPage() }} / {{ $signatures->lastPage() }}</span>
        @if ($signatures->nextPageUrl())<a href="{{ $signatures->nextPageUrl() }}">Suivant →</a>@endif
    </nav>
@endif
@endsection
