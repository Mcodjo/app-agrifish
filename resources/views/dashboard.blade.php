@extends('layouts.espace')

@section('title', 'Vue d’ensemble')

@php
    $firstName = explode(' ', auth()->user()->name)[0];
    $progressOf = function ($item) {
        $total = $item->milestones->count();
        return $total ? (int) round($item->milestones->whereIn('status', ['validated', 'completed'])->count() / $total * 100) : 0;
    };
    $nextStep = fn ($item) => $item->milestones->first(fn ($m) => ! in_array($m->status, ['validated', 'completed']));
    $subtitle = [
        'client' => 'Vos projets de conseil, vos commandes et ce qui attend une action de votre part.',
        'expert' => 'Vos projets assignés, les échéances à venir et les points bloquants.',
        'admin' => 'L’activité du cabinet et de la boutique en un coup d’œil.',
    ][$role];
@endphp

@section('content')
<x-espace.header :title="'Bonjour '.$firstName" :subtitle="$subtitle">
    <x-slot:actions>
        @if($role === 'client')
            <a href="{{ route('shop.index') }}" class="btn-outline">Boutique</a>
            <a href="{{ route('requests.create') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                Nouvelle demande
            </a>
        @elseif($role === 'admin')
            <a href="{{ route('shop-admin.orders.index', ['statut' => 'a-traiter']) }}" class="btn-outline">Commandes à traiter</a>
            <a href="{{ route('admin.requests.index') }}" class="btn-primary">Traiter les demandes</a>
        @else
            <a href="{{ route('projects.index') }}" class="btn-primary">Mes projets</a>
        @endif
    </x-slot:actions>
</x-espace.header>

<x-espace.body class="space-y-8">
    {{-- Chiffres clés --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @if($role === 'admin')
            <x-espace.stat label="Demandes ce mois-ci" :value="$indicators['this_month']" :href="route('admin.requests.index')" />
            <x-espace.stat label="Sans expert assigné" :value="$indicators['unassigned']" :tone="$indicators['unassigned'] ? 'warning' : null" :href="route('admin.requests.index')" />
            <x-espace.stat label="Ventes du mois" :value="money($shop['month'])" :href="route('shop-admin.dashboard')" />
            <x-espace.stat label="Commandes à traiter" :value="$shop['to_process']" :tone="$shop['to_process'] ? 'warning' : null" :href="route('shop-admin.orders.index', ['statut' => 'a-traiter'])" />
        @elseif($role === 'expert')
            <x-espace.stat label="Projets en cours" :value="$stats['open']" :href="route('projects.index', ['filtre' => 'en-cours'])" />
            <x-espace.stat label="Échéances sous 14 jours" :value="$deadlines->count()" :tone="$deadlines->count() ? 'warning' : null" />
            <x-espace.stat label="Actions requises" :value="$stats['alerts']" :tone="$stats['alerts'] ? 'warning' : null" />
            <x-espace.stat label="Projets terminés" :value="$stats['completed']" />
        @else
            <x-espace.stat label="Projets en cours" :value="$stats['open']" :href="route('projects.index', ['filtre' => 'en-cours'])" />
            <x-espace.stat label="Actions requises" :value="$stats['alerts']" :tone="$stats['alerts'] ? 'warning' : null" />
            <x-espace.stat label="Commandes en cours" :value="$orders->whereIn('status', \App\Models\Shop\Order::OPEN)->count()" :href="route('account.orders.index')" />
            <x-espace.stat label="Projets terminés" :value="$stats['completed']" />
        @endif
    </div>

    <div class="grid items-start gap-6 lg:grid-cols-[1fr_22rem]">
        <div class="space-y-6">
            {{-- Projets en cours --}}
            <x-espace.section :title="$role === 'admin' ? 'Projets récemment mis à jour' : 'Projets en cours'" flush>
                <x-slot:actions>
                    <a href="{{ route('projects.index') }}" class="text-sm text-muted hover:text-dark">Tout voir</a>
                </x-slot:actions>
                @forelse($openProjects as $project)
                    @php $progress = $progressOf($project); $step = $nextStep($project); @endphp
                    <a href="{{ route('projects.show', $project) }}" class="block border-b border-line px-5 py-4 last:border-0 hover:bg-cream/40">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-xs text-muted">{{ $project->reference }} · {{ $role === 'client' ? ($project->assignedExpert?->name ?? 'Expert à désigner') : ($project->client?->name ?? 'Client supprimé') }}</p>
                                <p class="mt-0.5 font-medium text-dark">{{ $project->title }}</p>
                            </div>
                            <x-status-badge :status="$project->status" />
                        </div>
                        <div class="mt-3 flex items-center gap-3">
                            <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-cream" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100" aria-label="Avancement">
                                <div class="h-full rounded-full bg-primary-500" style="width: {{ $progress }}%"></div>
                            </div>
                            <span class="w-10 text-right text-sm font-medium text-dark">{{ $progress }} %</span>
                        </div>
                        <p class="mt-2 truncate text-sm text-muted">
                            {{ $step ? 'Étape en cours : '.$step->title : ($project->milestones->isEmpty() ? 'Étapes en cours de planification' : 'Toutes les étapes sont validées') }}
                        </p>
                    </a>
                @empty
                    <div class="px-5 py-10 text-center">
                        <p class="font-medium text-dark">Aucun projet en cours</p>
                        @if($role === 'client')
                            <p class="mt-1 text-sm text-muted">Décrivez votre besoin : un expert l’analyse et vous propose un plan d’action.</p>
                            <a href="{{ route('requests.create') }}" class="btn-primary mt-4">Déposer une demande</a>
                        @endif
                    </div>
                @endforelse
            </x-espace.section>

            @if($role === 'client')
                <x-espace.section title="Dernières commandes" flush>
                    <x-slot:actions>
                        <a href="{{ route('account.orders.index') }}" class="text-sm text-muted hover:text-dark">Toutes mes commandes</a>
                    </x-slot:actions>
                    @forelse($orders as $order)
                        <a href="{{ route('account.orders.show', $order) }}" class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-3.5 last:border-0 hover:bg-cream/40">
                            <div>
                                <p class="font-medium text-dark">{{ $order->number }}</p>
                                <p class="text-sm text-muted">{{ $order->created_at->translatedFormat('d F Y') }} · {{ $order->items_count }} {{ $order->items_count > 1 ? 'articles' : 'article' }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-medium text-dark">{{ money($order->total) }}</span>
                                <x-status-badge :status="$order->status" :label="$order->statusLabel()" />
                            </div>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-muted">
                            Aucune commande pour l’instant. <a href="{{ route('shop.index') }}" class="font-medium text-dark underline underline-offset-2">Découvrir la boutique</a>
                        </div>
                    @endforelse
                </x-espace.section>
            @endif

            @if($role === 'admin')
                <div class="grid gap-6 md:grid-cols-2">
                    <x-espace.section title="Demandes par statut">
                        @if($indicators['by_status'])
                            @php $max = max(array_column($indicators['by_status'], 'total')); @endphp
                            <ul class="space-y-2.5">
                                @foreach($indicators['by_status'] as $key => $row)
                                    <li class="grid grid-cols-[8.5rem_1fr_2rem] items-center gap-3 text-sm">
                                        <a href="{{ route('admin.requests.index', ['statut' => $key]) }}" class="truncate text-muted hover:text-dark">{{ $row['label'] }}</a>
                                        <span class="h-2 overflow-hidden rounded-full bg-cream" aria-hidden="true"><span class="block h-full rounded-full bg-primary-500" style="width: {{ max(4, round($row['total'] / $max * 100)) }}%"></span></span>
                                        <span class="text-right font-medium text-dark">{{ $row['total'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-muted">Aucune demande pour l’instant.</p>
                        @endif
                        <p class="mt-4 border-t border-line pt-3 text-sm text-muted">
                            Délai moyen de prise en charge :
                            <span class="font-medium text-dark">
                                @if($indicators['average_handling_hours'] === null) pas encore de données
                                @elseif($indicators['average_handling_hours'] < 48) {{ $indicators['average_handling_hours'] }} h
                                @else {{ round($indicators['average_handling_hours'] / 24, 1) }} jours
                                @endif
                            </span>
                        </p>
                    </x-espace.section>

                    <x-espace.section title="Charge des experts" flush>
                        <x-slot:actions><a href="{{ route('admin.users.index', ['role' => 'expert']) }}" class="text-sm text-muted hover:text-dark">Gérer</a></x-slot:actions>
                        <ul class="divide-y divide-line">
                            @forelse($indicators['experts'] as $expert)
                                <li class="flex items-center justify-between gap-3 px-5 py-2.5 text-sm">
                                    <span class="truncate text-dark">{{ $expert->name }}</span>
                                    <span class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $expert->open_projects_count >= 5 ? 'bg-amber-50 text-amber-800' : 'bg-cream text-dark' }}">{{ $expert->open_projects_count }} en cours</span>
                                </li>
                            @empty
                                <li class="px-5 py-4 text-sm text-muted">Aucun expert. <a href="{{ route('admin.users.create') }}" class="font-medium text-dark underline underline-offset-2">Créer un compte expert</a></li>
                            @endforelse
                        </ul>
                    </x-espace.section>
                </div>
            @endif
        </div>

        <aside class="space-y-6">
            <x-espace.section title="Actions requises" flush>
                @forelse($alerts as $alert)
                    <a href="{{ $alert['url'] }}" class="block border-b border-l-[3px] border-b-line px-4 py-3 last:border-b-0 hover:bg-cream/40 {{ $alert['tone'] === 'red' ? 'border-l-red-500' : 'border-l-amber-500' }}">
                        <p class="text-xs text-muted">{{ $alert['reference'] }}</p>
                        <p class="mt-0.5 text-sm font-medium text-dark">{{ $alert['message'] }}</p>
                        <p class="mt-0.5 truncate text-xs text-muted">{{ $alert['title'] }}</p>
                    </a>
                @empty
                    <p class="px-5 py-5 text-sm text-muted">Rien à signaler, tout est à jour.</p>
                @endforelse
            </x-espace.section>

            @if($role === 'expert')
                <x-espace.section title="Échéances à venir" flush>
                    @forelse($deadlines as $milestone)
                        @php $late = $milestone->due_date->isPast(); @endphp
                        <a href="{{ route('projects.show', $milestone->serviceRequest) }}#etapes" class="block border-b border-line px-5 py-3 last:border-0 hover:bg-cream/40">
                            <p class="text-sm font-medium text-dark">{{ $milestone->title }}</p>
                            <p class="mt-0.5 text-xs {{ $late ? 'font-medium text-red-700' : 'text-muted' }}">{{ $late ? 'En retard · ' : '' }}{{ $milestone->due_date->translatedFormat('d M Y') }} · {{ $milestone->serviceRequest->reference }}</p>
                        </a>
                    @empty
                        <p class="px-5 py-5 text-sm text-muted">Aucune échéance dans les 14 prochains jours.</p>
                    @endforelse
                </x-espace.section>
            @endif

            @if($role === 'admin')
                <x-espace.section title="Boutique">
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-muted">Ventes du mois</dt><dd class="font-medium text-dark">{{ money($shop['month']) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-muted">Commandes à traiter</dt><dd class="font-medium text-dark">{{ $shop['to_process'] }}</dd></div>
                        <div class="flex justify-between"><dt class="text-muted">Avis à valider</dt><dd class="font-medium text-dark">{{ $shop['reviews'] }}</dd></div>
                    </dl>
                    <a href="{{ route('shop-admin.dashboard') }}" class="btn-outline mt-4 w-full">Tableau de bord boutique</a>
                </x-espace.section>
            @endif

            @if($role === 'client')
                <x-espace.section title="Besoin d’aide ?">
                    <p class="text-sm leading-6 text-muted">Pour un projet, écrivez à votre expert depuis la page du projet. Pour une commande ou toute autre question : <a href="mailto:{{ config('site.contact.email') }}" class="text-dark underline underline-offset-2">{{ config('site.contact.email') }}</a>.</p>
                </x-espace.section>
            @endif
        </aside>
    </div>
</x-espace.body>
@endsection
