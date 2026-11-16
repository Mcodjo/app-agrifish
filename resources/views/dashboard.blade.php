@extends('layouts.app')

@section('title', 'Mon espace')
@section('meta_description', 'Tableau de bord AgriFish : vos projets, leurs statuts et les actions à mener.')

@php
    $copy = [
        'client' => ['eyebrow' => 'Espace client', 'subtitle' => 'Suivez l’avancement de vos projets, vos documents et vos échanges avec nos experts.', 'list' => 'Mes projets'],
        'expert' => ['eyebrow' => 'Espace expert', 'subtitle' => 'Les projets qui vous sont assignés : jalons, livrables et échanges avec les clients.', 'list' => 'Projets assignés'],
        'admin' => ['eyebrow' => 'Administration', 'subtitle' => 'Vue d’ensemble de toutes les demandes clients et de leur avancement.', 'list' => 'Toutes les demandes'],
    ][$role];
    $firstName = explode(' ', auth()->user()->name)[0];
    $progressOf = function ($item) {
        $total = $item->milestones->count();
        return $total ? (int) round($item->milestones->whereIn('status', ['validated', 'completed'])->count() / $total * 100) : 0;
    };
@endphp

@section('content')
<x-app-header :eyebrow="$copy['eyebrow']" :title="'Bonjour, '.$firstName" :subtitle="$copy['subtitle']">
    <x-slot:actions>
        @if($role === 'client')
            <a href="{{ route('requests.create') }}" class="btn-gold">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nouvelle demande
            </a>
        @elseif($role === 'admin')
            <a href="{{ route('admin.requests.index') }}" class="btn-gold">Gérer les demandes</a>
        @endif
        <a href="{{ route('profile.edit') }}" class="btn-ghost">Mon profil</a>
    </x-slot:actions>

    <dl class="mt-8 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach([
            ['Projets', $stats['total'], 'M3 7h18M3 12h18M3 17h18'],
            ['En cours', $stats['open'], 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Terminés', $stats['completed'], 'M5 13l4 4L19 7'],
            ['À suivre', $stats['alerts'], 'M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z'],
        ] as [$label, $value, $icon])
            <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-gold-light">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                </span>
                <div>
                    <dd class="text-2xl font-extrabold leading-none">{{ $value }}</dd>
                    <dt class="mt-1 text-xs font-medium text-white/70">{{ $label }}</dt>
                </div>
            </div>
        @endforeach
    </dl>
</x-app-header>

<section class="bg-cream py-10 lg:py-14">
    <div class="max-w-content grid gap-6 px-4 lg:grid-cols-3 lg:px-8">

        {{-- Actions requises --}}
        <aside class="card h-fit lg:order-2">
            <h2 class="flex items-center gap-2 text-lg font-bold text-dark">
                Actions requises
                @if($alerts->isNotEmpty())
                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-800">{{ $alerts->count() }}</span>
                @endif
            </h2>
            <div class="mt-4 space-y-3">
                @forelse($alerts as $alert)
                    <a href="{{ $alert['url'] }}" class="group block rounded-xl border-l-4 p-3.5 transition hover:shadow-md {{ $alert['tone'] === 'red' ? 'border-red-400 bg-red-50/60' : 'border-amber-400 bg-amber-50/60' }}">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ $alert['reference'] }}</p>
                        <p class="mt-0.5 text-sm font-semibold text-dark">{{ $alert['message'] }}</p>
                        <p class="mt-0.5 truncate text-xs text-gray-500">{{ $alert['title'] }}</p>
                    </a>
                @empty
                    <div class="flex items-center gap-3 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Rien à signaler, tout est à jour.
                    </div>
                @endforelse
            </div>
        </aside>

        {{-- Liste des projets --}}
        <div class="lg:order-1 lg:col-span-2">
            <div class="mb-4 flex items-end justify-between gap-4">
                <h2 class="text-xl font-extrabold text-dark">{{ $copy['list'] }}</h2>
                <span class="text-sm text-gray-500">{{ $requests->count() }} {{ \Illuminate\Support\Str::plural('projet', $requests->count()) }}</span>
            </div>

            <div class="space-y-3">
                @forelse($requests as $requestItem)
                    @php $progress = $progressOf($requestItem); @endphp
                    <a href="{{ route('projects.show', $requestItem) }}"
                       class="group block rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-0.5 hover:shadow-lg hover:ring-primary-200">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">
                                    {{ $requestItem->reference }} · {{ \App\Models\ServiceRequest::SERVICE_TYPES[$requestItem->service_type] ?? $requestItem->service_type }}
                                </p>
                                <h3 class="mt-1 text-lg font-bold text-dark group-hover:text-primary">{{ $requestItem->title }}</h3>
                            </div>
                            <x-status-badge :status="$requestItem->status" />
                        </div>

                        <div class="mt-4">
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>
                                    @if($requestItem->milestones->isNotEmpty())
                                        Étape actuelle : <span class="font-medium text-gray-700">{{ ($requestItem->milestones->firstWhere(fn ($m) => ! in_array($m->status, ['validated', 'completed'])) ?? $requestItem->milestones->last())->title }}</span>
                                    @else
                                        Planification des étapes en attente
                                    @endif
                                </span>
                                <span class="font-bold text-primary">{{ $progress }}%</span>
                            </div>
                            <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-gray-100" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="h-full rounded-full bg-gradient-to-r from-primary to-primary-light" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-gray-500">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $role === 'client' ? ($requestItem->assignedExpert?->name ?? 'Expert à assigner') : ($requestItem->client?->name ?? '—') }}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                {{ $requestItem->milestones_count }} {{ \Illuminate\Support\Str::plural('jalon', $requestItem->milestones_count) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.42-4.03 8-9 8a9.86 9.86 0 01-4.26-.95L3 20l1.4-3.72A7.96 7.96 0 013 12c0-4.42 4.03-8 9-8s9 3.58 9 8z"/></svg>
                                {{ $requestItem->messages_count }} {{ \Illuminate\Support\Str::plural('message', $requestItem->messages_count) }}
                            </span>
                            <span class="ml-auto font-semibold text-primary">Ouvrir →</span>
                        </div>
                    </a>
                @empty
                    <div class="rounded-2xl border-2 border-dashed border-gray-200 bg-white px-6 py-14 text-center">
                        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary-50 text-primary">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.59a1 1 0 01.7.29l5.42 5.42a1 1 0 01.29.7V19a2 2 0 01-2 2z"/></svg>
                        </span>
                        @if($role === 'client')
                            <h3 class="mt-4 text-lg font-bold text-dark">Aucun projet pour l’instant</h3>
                            <p class="mx-auto mt-2 max-w-sm text-sm text-gray-500">Décrivez votre besoin en quelques minutes : un expert AgriFish l’analyse et vous propose un plan d’action.</p>
                            <a href="{{ route('requests.create') }}" class="btn-primary mt-6">Déposer ma première demande</a>
                        @else
                            <h3 class="mt-4 text-lg font-bold text-dark">Aucun projet assigné</h3>
                            <p class="mx-auto mt-2 max-w-sm text-sm text-gray-500">Les projets apparaîtront ici dès qu’ils vous seront assignés.</p>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
