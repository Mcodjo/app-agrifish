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
    <div class="grid gap-8 lg:grid-cols-[1fr_20rem]">

        <div>
            <div class="mb-4 flex items-baseline justify-between gap-4">
                <h2 class="text-lg font-semibold text-dark">{{ $copy['list'] }}</h2>
                <span class="text-sm text-muted">{{ $requests->count() }} {{ \Illuminate\Support\Str::plural('projet', $requests->count()) }}</span>
            </div>

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
                        @if($role === 'client')
                            <h3 class="text-lg font-semibold text-dark">Aucun projet pour l’instant</h3>
                            <p class="mx-auto mt-2 max-w-sm text-[15px] leading-7 text-muted">Décrivez votre besoin : un expert AgriFish l’analyse et vous propose un plan d’action.</p>
                            <a href="{{ route('requests.create') }}" class="btn-primary mt-6">Déposer ma première demande</a>
                        @else
                            <h3 class="text-lg font-semibold text-dark">Aucun projet assigné</h3>
                            <p class="mx-auto mt-2 max-w-sm text-[15px] leading-7 text-muted">Les projets apparaîtront ici dès qu’ils vous seront assignés.</p>
                        @endif
                    </div>
                @endforelse
            </div>
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
