@extends('layouts.espace')

@php
    $title = ['client' => 'Mes projets', 'expert' => 'Projets assignés', 'admin' => 'Projets'][$role];
    $progressOf = function ($item) {
        $total = $item->milestones->count();
        return $total ? (int) round($item->milestones->whereIn('status', ['validated', 'completed'])->count() / $total * 100) : 0;
    };
@endphp

@section('title', $title)

@section('content')
<x-espace.header :title="$title" :subtitle="$role === 'client' ? 'Toutes vos demandes de conseil, d’étude ou de formation, et leur avancement.' : 'Suivi de l’avancement, des étapes et des échanges de chaque projet.'">
    @if($role === 'client')
        <x-slot:actions>
            <a href="{{ route('requests.create') }}" class="btn-primary">Nouvelle demande</a>
        </x-slot:actions>
    @endif
</x-espace.header>

<x-espace.body class="space-y-5">
    @if($total > 0)
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <x-espace.filters :tabs="['' => 'Tous'] + $filters" :current="$currentFilter" :keep="['q' => $search]" />
            <form method="GET" class="relative md:w-72">
                @if($currentFilter)<input type="hidden" name="filtre" value="{{ $currentFilter }}">@endif
                <label for="project-search" class="sr-only">Rechercher</label>
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="project-search" type="search" name="q" value="{{ $search }}" placeholder="Titre ou référence" class="field-input !py-2 pl-9 text-sm">
            </form>
        </div>
    @endif

    @if($projects->isEmpty())
        @if($currentFilter || $search)
            <x-espace.empty title="Aucun projet ne correspond" text="Modifiez la recherche ou le filtre.">
                <a href="{{ route('projects.index') }}" class="btn-outline">Voir tous les projets</a>
            </x-espace.empty>
        @elseif($role === 'client')
            <x-espace.empty title="Aucun projet pour l’instant" text="Décrivez votre besoin : un expert AgriFish l’analyse et vous propose un plan d’action.">
                <a href="{{ route('requests.create') }}" class="btn-primary">Déposer ma première demande</a>
            </x-espace.empty>
        @else
            <x-espace.empty title="Aucun projet" text="Les projets apparaîtront ici dès qu’ils vous seront assignés." />
        @endif
    @else
        <div class="overflow-hidden rounded-xl border border-line bg-white">
            <div class="hidden grid-cols-[1fr_10rem_9rem_8rem] gap-4 border-b border-line bg-cream/50 px-5 py-2.5 text-xs font-medium text-muted md:grid">
                <span>Projet</span><span>{{ $role === 'client' ? 'Expert' : 'Client' }}</span><span>Avancement</span><span>Statut</span>
            </div>
            <ul class="divide-y divide-line">
                @foreach($projects as $project)
                    @php $progress = $progressOf($project); @endphp
                    <li>
                        <a href="{{ route('projects.show', $project) }}" class="grid gap-3 px-5 py-4 hover:bg-cream/40 md:grid-cols-[1fr_10rem_9rem_8rem] md:items-center md:gap-4">
                            <div class="min-w-0">
                                <p class="text-xs text-muted">{{ $project->reference }} · {{ \App\Models\ServiceRequest::SERVICE_TYPES[$project->service_type] ?? $project->service_type }} · {{ $project->created_at->format('d/m/Y') }}</p>
                                <p class="mt-0.5 truncate font-medium text-dark">{{ $project->title }}</p>
                                <p class="mt-0.5 text-xs text-muted">{{ $project->milestones_count }} {{ $project->milestones_count > 1 ? 'étapes' : 'étape' }} · {{ $project->messages_count }} {{ $project->messages_count > 1 ? 'messages' : 'message' }}</p>
                            </div>
                            <span class="truncate text-sm text-dark">{{ $role === 'client' ? ($project->assignedExpert?->name ?? 'À désigner') : ($project->client?->name ?? '—') }}</span>
                            <span class="flex items-center gap-2">
                                <span class="h-1.5 flex-1 overflow-hidden rounded-full bg-cream"><span class="block h-full rounded-full bg-primary-500" style="width: {{ $progress }}%"></span></span>
                                <span class="w-9 text-right text-sm text-dark">{{ $progress }} %</span>
                            </span>
                            <span><x-status-badge :status="$project->status" /></span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div>{{ $projects->links() }}</div>
    @endif
</x-espace.body>
@endsection
