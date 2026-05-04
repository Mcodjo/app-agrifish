@extends('layouts.app')

@section('title', 'Mon espace')
@section('meta_description', 'Tableau de bord client AgriFish avec résumé des projets, statuts et alertes.')

@section('content')
<section class="relative overflow-hidden bg-primary-dark text-white">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
    <div class="max-w-content mx-auto px-4 lg:px-8 pt-20 pb-20 lg:pt-28 lg:pb-24 relative z-10">
        <div class="grid gap-10 lg:grid-cols-[1.15fr_0.85fr] lg:items-end">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.24em] backdrop-blur">Espace client</span>
                <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-7xl">Bienvenue, {{ auth()->user()->name }}.</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-white/78 lg:text-xl">Suivez vos projets, vos statuts et vos alertes depuis un seul point d’accès.</p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                    <a href="{{ route('requests.create') }}" class="inline-flex items-center justify-center rounded-xl bg-gold px-7 py-4 font-bold text-white transition hover:bg-gold-dark">Nouvelle demande</a>
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center rounded-xl border border-white/30 px-7 py-4 font-semibold text-white transition hover:bg-white/10">Mon profil</a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['Total', $stats['total']],
                    ['Ouverts', $stats['open']],
                    ['Terminés', $stats['completed']],
                    ['Alertes', $stats['alerts']],
                ] as $stat)
                    <div class="rounded-3xl border border-white/15 bg-white/10 p-5 backdrop-blur-md shadow-xl">
                        <div class="text-3xl font-extrabold text-gold-light">{{ $stat[1] }}</div>
                        <div class="mt-2 text-sm font-medium text-white/72">{{ $stat[0] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="bg-cream py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8 grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-gold">Alertes</p>
            <div class="mt-4 space-y-4">
                @forelse($alerts as $alert)
                    <div class="rounded-2xl bg-cream p-4">
                        <div class="font-bold text-dark">{{ $alert['title'] }}</div>
                        <p class="mt-1 text-sm text-gray-600">{{ $alert['message'] }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucune alerte pour le moment.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100 lg:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-gold">Résumé projets</p>
                    <h2 class="mt-2 text-2xl font-extrabold text-dark">Vos demandes récentes</h2>
                </div>
                <a href="{{ route('requests.create') }}" class="inline-flex items-center rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white">Créer une demande</a>
            </div>

            <div class="mt-6 overflow-hidden rounded-3xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-cream text-left text-xs font-bold uppercase tracking-[0.18em] text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Référence</th>
                            <th class="px-4 py-3">Service</th>
                            <th class="px-4 py-3">Statut</th>
                            <th class="px-4 py-3">Expert</th>
                            <th class="px-4 py-3">Accès</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($requests as $requestItem)
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-dark">{{ $requestItem->reference }}</div>
                                    <div class="text-sm text-gray-500">{{ $requestItem->title }}</div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $requestItem->service_type }}</td>
                                <td class="px-4 py-4 text-sm">
                                    <span class="inline-flex rounded-full bg-primary/10 px-3 py-1 font-semibold text-primary">{{ str_replace('_', ' ', $requestItem->status) }}</span>
                                    <div class="mt-2 text-xs text-gray-500">{{ $requestItem->milestones_count }} jalons · {{ $requestItem->messages_count }} messages</div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">
                                    <div>{{ $requestItem->assignedExpert?->name ?? 'Non assigné' }}</div>
                                    @if($requestItem->milestones->isNotEmpty())
                                        <div class="mt-2 text-xs text-gray-500">Dernier jalon: {{ $requestItem->milestones->last()->title }} ({{ str_replace('_', ' ', $requestItem->milestones->last()->status) }})</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">
                                    <a href="{{ route('projects.show', $requestItem) }}" class="font-semibold text-primary hover:text-primary-dark">Voir le projet</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Aucune demande enregistrée. Lancez votre première demande de service.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
