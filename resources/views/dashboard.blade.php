@extends('layouts.app')

@section('title', 'Mon espace')
@section('espace', true)

@php
    $copy = [
        'client' => ['eyebrow' => 'Espace client', 'subtitle' => 'L’avancement de vos projets, vos documents et vos échanges avec nos experts.', 'list' => 'Mes projets'],
        'expert' => ['eyebrow' => 'Espace expert', 'subtitle' => 'Les projets qui vous sont assignés : étapes, livrables et échanges avec les clients.', 'list' => 'Projets assignés'],
        'admin' => ['eyebrow' => 'Administration', 'subtitle' => 'Toutes les demandes clients et leur avancement.', 'list' => 'Toutes les demandes'],
    ][$role];
    $firstName = explode(' ', auth()->user()->name)[0];
    $progressOf = function ($item) {
        $total = $item->milestones->count();
        return $total ? (int) round($item->milestones->whereIn('status', ['validated', 'completed'])->count() / $total * 100) : 0;
    };
@endphp

@section('content')
<x-app-header :eyebrow="$copy['eyebrow']" :title="'Bonjour '.$firstName" :subtitle="$copy['subtitle']">
    <x-slot:actions>
        @if($role === 'client')
            <a href="{{ route('requests.create') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                Nouvelle demande
            </a>
        @elseif($role === 'admin')
            <a href="{{ route('admin.users.index') }}" class="btn-outline">Utilisateurs</a>
            <a href="{{ route('admin.requests.index') }}" class="btn-primary">Gérer les demandes</a>
        @endif
    </x-slot:actions>

    <dl class="mt-8 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach([
            ['Projets', $stats['total']],
            ['En cours', $stats['open']],
            ['Terminés', $stats['completed']],
            ['Actions requises', $stats['alerts']],
        ] as [$label, $value])
            <div class="rounded-xl border border-line bg-white px-4 py-4 sm:px-5">
                <dt class="text-sm text-muted">{{ $label }}</dt>
                <dd class="mt-1 text-2xl font-semibold text-dark">{{ $value }}</dd>
            </div>
        @endforeach
    </dl>
</x-app-header>

<section class="max-w-content px-site py-10 lg:py-12">
    @if($indicators)
        <div class="mb-10 grid gap-4 lg:grid-cols-3">
            <div class="card lg:col-span-2">
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <h2 class="text-[17px] font-semibold text-dark">Indicateurs</h2>
                    <span class="text-sm text-muted">{{ $indicators['clients'] }} {{ $indicators['clients'] > 1 ? 'clients inscrits' : 'client inscrit' }}</span>
                </div>
                <dl class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-xl bg-cream px-4 py-3">
                        <dt class="text-sm text-muted">Demandes ce mois-ci</dt>
                        <dd class="mt-1 text-2xl font-semibold text-dark">{{ $indicators['this_month'] }}</dd>
                    </div>
                    <div class="rounded-xl bg-cream px-4 py-3">
                        <dt class="text-sm text-muted">Sans expert assigné</dt>
                        <dd class="mt-1 text-2xl font-semibold {{ $indicators['unassigned'] ? 'text-amber-800' : 'text-dark' }}">{{ $indicators['unassigned'] }}</dd>
                    </div>
                    <div class="rounded-xl bg-cream px-4 py-3">
                        <dt class="text-sm text-muted">Délai moyen de prise en charge</dt>
                        <dd class="mt-1 text-2xl font-semibold text-dark">
                            @if($indicators['average_handling_hours'] === null)
                                <span class="text-base font-normal text-muted">Pas encore de données</span>
                            @elseif($indicators['average_handling_hours'] < 48)
                                {{ $indicators['average_handling_hours'] }} h
                            @else
                                {{ round($indicators['average_handling_hours'] / 24, 1) }} j
                            @endif
                        </dd>
                    </div>
                </dl>
                @if($indicators['by_status'])
                    @php $max = max(array_column($indicators['by_status'], 'total')); @endphp
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-dark">Demandes par statut</h3>
                        <ul class="mt-3 space-y-2.5">
                            @foreach($indicators['by_status'] as $key => $row)
                                <li class="grid grid-cols-[9rem_1fr_2.5rem] items-center gap-3 text-sm">
                                    <a href="{{ route('admin.requests.index', ['statut' => $key]) }}" class="truncate text-muted hover:text-dark">{{ $row['label'] }}</a>
                                    <span class="h-2 overflow-hidden rounded-full bg-cream" aria-hidden="true">
                                        <span class="block h-full rounded-full bg-primary-500" style="width: {{ max(4, round($row['total'] / $max * 100)) }}%"></span>
                                    </span>
                                    <span class="text-right font-medium text-dark">{{ $row['total'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <p class="mt-5 text-xs text-muted">Délai calculé sur les demandes des 90 derniers jours, entre le dépôt et le premier changement de statut ou la première assignation.</p>
            </div>
            <div class="card">
                <div class="flex items-baseline justify-between gap-2">
                    <h2 class="text-[17px] font-semibold text-dark">Charge des experts</h2>
                    <a href="{{ route('admin.users.index', ['role' => 'expert']) }}" class="text-sm text-muted hover:text-dark">Gérer</a>
                </div>
                <ul class="mt-4 divide-y divide-line">
                    @forelse($indicators['experts'] as $expert)
                        <li class="flex items-center justify-between gap-3 py-2.5 text-sm">
                            <span class="truncate text-dark">{{ $expert->name }}</span>
                            <span class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $expert->open_projects_count >= 5 ? 'bg-amber-50 text-amber-800' : 'bg-cream text-dark' }}">
                                {{ $expert->open_projects_count }} en cours
                            </span>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-muted">
                            Aucun expert pour l’instant. <a href="{{ route('admin.users.create') }}" class="font-medium text-dark underline underline-offset-2">Créer un compte expert</a>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    @endif

    <div class="grid gap-8 lg:grid-cols-[1fr_20rem]">

        <div>
            <div class="mb-4 flex items-baseline justify-between gap-4">
                <h2 class="text-lg font-semibold text-dark">{{ $copy['list'] }}</h2>
                <span class="text-sm text-muted">{{ $requests->total() }} {{ \Illuminate\Support\Str::plural('projet', $requests->total()) }}</span>
            </div>

            @if($stats['total'] > 0)
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <nav class="-mx-1 flex gap-2 overflow-x-auto px-1 pb-1 sm:pb-0" aria-label="Filtrer les projets">
                        @foreach(['' => 'Tous'] + $filters as $key => $label)
                            @php $active = (string) $currentFilter === (string) $key; @endphp
                            <a href="{{ route('dashboard', array_filter(['filtre' => $key, 'q' => $search])) }}"
                               class="shrink-0 rounded-full px-3.5 py-1.5 text-sm font-medium transition {{ $active ? 'bg-dark text-white' : 'bg-cream text-dark hover:bg-sand' }}"
                               @if($active) aria-current="true" @endif>{{ $label }}</a>
                        @endforeach
                    </nav>
                    <form method="GET" action="{{ route('dashboard') }}" class="relative sm:ml-auto sm:w-64">
                        @if($currentFilter)<input type="hidden" name="filtre" value="{{ $currentFilter }}">@endif
                        <label for="dashboard-search" class="sr-only">Rechercher un projet</label>
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input id="dashboard-search" type="search" name="q" value="{{ $search }}" placeholder="Titre ou référence" class="field-input !py-2 pl-9 text-sm">
                    </form>
                </div>
            @endif

            <div class="space-y-3">
                @forelse($requests as $requestItem)
                    @php $progress = $progressOf($requestItem); @endphp
                    <a href="{{ route('projects.show', $requestItem) }}"
                       class="group block rounded-xl border border-line bg-white p-5 transition-colors hover:border-dark/25">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs text-muted">
                                    {{ $requestItem->reference }} · {{ \App\Models\ServiceRequest::SERVICE_TYPES[$requestItem->service_type] ?? $requestItem->service_type }}
                                </p>
                                <h3 class="mt-1 text-[17px] font-semibold text-dark">{{ $requestItem->title }}</h3>
                            </div>
                            <x-status-badge :status="$requestItem->status" />
                        </div>

                        <div class="mt-4">
                            <div class="flex items-center justify-between gap-4 text-sm text-muted">
                                <span class="truncate">
                                    @if($requestItem->milestones->isNotEmpty())
                                        Étape en cours : <span class="text-dark">{{ ($requestItem->milestones->firstWhere(fn ($m) => ! in_array($m->status, ['validated', 'completed'])) ?? $requestItem->milestones->last())->title }}</span>
                                    @else
                                        Étapes en cours de planification
                                    @endif
                                </span>
                                <span class="shrink-0 font-medium text-dark">{{ $progress }} %</span>
                            </div>
                            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-cream" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100" aria-label="Avancement">
                                <div class="h-full rounded-full bg-primary-500" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-1 text-sm text-muted">
                            <span>{{ $role === 'client' ? ($requestItem->assignedExpert?->name ?? 'Expert à désigner') : ($requestItem->client?->name ?? 'Client supprimé') }}</span>
                            <span>{{ $requestItem->milestones_count }} {{ \Illuminate\Support\Str::plural('étape', $requestItem->milestones_count) }}</span>
                            <span>{{ $requestItem->messages_count }} {{ \Illuminate\Support\Str::plural('message', $requestItem->messages_count) }}</span>
                            <span class="ml-auto text-dark group-hover:underline">Ouvrir</span>
                        </div>
                    </a>
                @empty
                    <div class="rounded-xl border border-dashed border-line bg-white px-6 py-14 text-center">
                        @if($currentFilter || $search)
                            <h3 class="text-lg font-semibold text-dark">Aucun projet ne correspond</h3>
                            <p class="mx-auto mt-2 max-w-sm text-[15px] leading-7 text-muted">Modifiez la recherche ou le filtre.</p>
                            <a href="{{ route('dashboard') }}" class="btn-outline mt-6">Voir tous les projets</a>
                        @elseif($role === 'client')
                            <h3 class="text-lg font-semibold text-dark">Aucun projet pour l’instant</h3>
                            <p class="mx-auto mt-2 max-w-sm text-[15px] leading-7 text-muted">Décrivez votre besoin : un expert AgriFish l’analyse et vous propose un plan d’action.</p>
                            <a href="{{ route('requests.create') }}" class="btn-primary mt-6">Déposer ma première demande</a>
                        @elseif($role === 'admin')
                            <h3 class="text-lg font-semibold text-dark">Aucune demande pour l’instant</h3>
                            <p class="mx-auto mt-2 max-w-sm text-[15px] leading-7 text-muted">Les demandes déposées par les clients apparaîtront ici.</p>
                        @else
                            <h3 class="text-lg font-semibold text-dark">Aucun projet assigné</h3>
                            <p class="mx-auto mt-2 max-w-sm text-[15px] leading-7 text-muted">Les projets apparaîtront ici dès qu’ils vous seront assignés.</p>
                        @endif
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $requests->links() }}</div>
        </div>

        <aside class="h-fit rounded-xl border border-line bg-white p-5">
            <h2 class="text-[15px] font-semibold text-dark">Actions requises</h2>
            <div class="mt-4 space-y-3">
                @forelse($alerts as $alert)
                    <a href="{{ $alert['url'] }}" class="block rounded-lg border-l-[3px] bg-cream/70 px-3.5 py-3 transition-colors hover:bg-cream {{ $alert['tone'] === 'red' ? 'border-red-500' : 'border-amber-500' }}">
                        <p class="text-xs text-muted">{{ $alert['reference'] }}</p>
                        <p class="mt-0.5 text-sm font-medium text-dark">{{ $alert['message'] }}</p>
                        <p class="mt-0.5 truncate text-xs text-muted">{{ $alert['title'] }}</p>
                    </a>
                @empty
                    <p class="text-sm leading-6 text-muted">Rien à signaler, tout est à jour.</p>
                @endforelse
            </div>
            @if($role === 'client')
                <div class="mt-6 border-t border-line pt-5 text-sm leading-6 text-muted">
                    Une question ? Écrivez à votre expert depuis la page du projet, ou à <a href="mailto:{{ config('site.contact.email') }}" class="text-dark underline underline-offset-2">{{ config('site.contact.email') }}</a>.
                </div>
            @endif
        </aside>
    </div>
</section>
@endsection
