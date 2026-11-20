@extends('layouts.app')

@section('title', 'Gestion des demandes')
@section('espace', true)

@section('content')
<x-app-header :back="route('dashboard')" back-label="Tableau de bord" eyebrow="Administration"
              title="Gestion des demandes"
              subtitle="Analysez les nouvelles demandes, assignez un expert et tenez le client informé." />

<section class="max-w-content px-site py-10 lg:py-12">
    <div class="space-y-4">

        {{-- Filtres --}}
        <div class="rounded-xl border border-line bg-white p-4">
            <form method="GET" action="{{ route('admin.requests.index') }}" class="flex flex-col gap-3 sm:flex-row">
                @if($currentStatus)<input type="hidden" name="statut" value="{{ $currentStatus }}">@endif
                <label for="q" class="sr-only">Rechercher</label>
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="q" type="search" name="q" value="{{ $search }}" placeholder="Référence, titre, nom ou e-mail du client…" class="field-input pl-11">
                </div>
                <button type="submit" class="btn-primary">Rechercher</button>
            </form>
            <nav class="-mx-1 mt-4 flex gap-2 overflow-x-auto px-1 pb-1" aria-label="Filtrer par statut">
                @php $tabs = ['' => ['Toutes', $counts->sum()]] + collect($statuses)->map(fn ($label, $key) => [$label, $counts[$key] ?? 0])->all(); @endphp
                @foreach($tabs as $key => [$label, $count])
                    @php $active = (string) $currentStatus === (string) $key; @endphp
                    <a href="{{ route('admin.requests.index', array_filter(['statut' => $key, 'q' => $search])) }}"
                       class="inline-flex shrink-0 items-center gap-2 rounded-full px-3.5 py-1.5 text-sm font-medium transition {{ $active ? 'bg-dark text-white' : 'bg-cream text-dark hover:bg-sand' }}">
                        {{ $label }}
                        <span class="rounded-full px-1.5 text-xs {{ $active ? 'bg-white/20' : 'bg-white text-muted' }}">{{ $count }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        @forelse($requests as $requestItem)
            <article class="rounded-xl border border-line bg-white p-5 sm:p-6" x-data="{ edit: false }">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs text-muted">{{ $requestItem->reference }}</span>
                            <x-status-badge :status="$requestItem->status" />
                            @if($requestItem->priority === 'high')
                                <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-800 ring-1 ring-inset ring-red-200">Urgente</span>
                            @endif
                        </div>
                        <h2 class="mt-1.5 text-[17px] font-semibold text-dark">
                            <a href="{{ route('projects.show', $requestItem) }}" class="hover:underline">{{ $requestItem->title }}</a>
                        </h2>
                        <p class="mt-1 line-clamp-2 text-sm leading-6 text-muted">{{ $requestItem->description }}</p>
                        <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1.5 text-xs text-muted">
                            <span><span class="font-medium text-dark">{{ $requestItem->client?->name }}</span> · {{ $requestItem->client?->email }}</span>
                            <span>{{ \App\Models\ServiceRequest::SERVICE_TYPES[$requestItem->service_type] ?? $requestItem->service_type }}</span>
                            <span>Reçue le {{ $requestItem->created_at->format('d/m/Y') }}</span>
                            <span>{{ count($requestItem->document_paths ?? []) }} document(s)</span>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-3 lg:flex-col lg:items-end">
                        <div class="text-sm">
                            <span class="text-muted">Expert :</span>
                            <span class="font-medium {{ $requestItem->assignedExpert ? 'text-dark' : 'text-amber-800' }}">{{ $requestItem->assignedExpert?->name ?? 'Non assigné' }}</span>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('projects.show', $requestItem) }}" class="btn-outline !py-2">Ouvrir</a>
                            <button type="button" @click="edit = !edit" class="btn-primary !py-2" x-text="edit ? 'Fermer' : 'Traiter'">Traiter</button>
                        </div>
                    </div>
                </div>

                <form x-show="edit" x-cloak x-transition method="POST" action="{{ route('admin.requests.update', $requestItem) }}"
                      class="mt-5 grid gap-4 rounded-xl bg-cream p-4 sm:grid-cols-2">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="field-label" for="status-{{ $requestItem->id }}">Statut</label>
                        <select id="status-{{ $requestItem->id }}" name="status" class="field-input">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($requestItem->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label" for="expert-{{ $requestItem->id }}">Expert assigné</label>
                        <select id="expert-{{ $requestItem->id }}" name="assigned_expert_id" class="field-input">
                            <option value="">Non assigné</option>
                            @foreach($experts as $expert)
                                <option value="{{ $expert->id }}" @selected((string) $requestItem->assigned_expert_id === (string) $expert->id)>{{ $expert->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="field-label" for="notes-{{ $requestItem->id }}">Note visible par le client</label>
                        <textarea id="notes-{{ $requestItem->id }}" name="status_notes" rows="3" maxlength="2000" class="field-input" placeholder="Ex : Nous avons besoin du plan de la parcelle pour avancer.">{{ $requestItem->status_notes }}</textarea>
                        <p class="mt-1.5 text-xs text-muted">Le client reçoit un e-mail si le statut change (selon ses préférences).</p>
                    </div>
                    <div class="flex justify-end gap-2 sm:col-span-2">
                        <button type="button" @click="edit = false" class="btn-outline">Annuler</button>
                        <button type="submit" class="btn-primary">Enregistrer</button>
                    </div>
                </form>
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-line bg-white px-6 py-14 text-center">
                <h2 class="text-lg font-semibold text-dark">Aucune demande trouvée</h2>
                <p class="mt-2 text-sm text-muted">Modifiez la recherche ou le filtre de statut.</p>
                @if($currentStatus || $search)
                    <a href="{{ route('admin.requests.index') }}" class="btn-outline mt-5">Réinitialiser les filtres</a>
                @endif
            </div>
        @endforelse

        <div>{{ $requests->links() }}</div>
    </div>
</section>
@endsection
