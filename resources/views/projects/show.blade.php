@extends('layouts.espace')

@section('title', $serviceRequest->reference.' · Projet')

@php
    $milestoneStatuses = ['planned' => 'Planifié', 'in_progress' => 'En cours', 'validated' => 'Validé', 'blocked' => 'Bloqué', 'completed' => 'Terminé'];
    $dotStyles = [
        'planned' => 'bg-white ring-line text-muted',
        'in_progress' => 'bg-primary-500 ring-primary-100 text-white',
        'validated' => 'bg-emerald-500 ring-emerald-200 text-white',
        'completed' => 'bg-emerald-500 ring-emerald-200 text-white',
        'blocked' => 'bg-red-500 ring-red-200 text-white',
    ];
    $fileMeta = function (string $path): array {
        $ext = strtoupper(pathinfo($path, PATHINFO_EXTENSION) ?: 'FICHIER');
        $color = match ($ext) {
            'PDF' => 'bg-red-50 text-red-600',
            'DOC', 'DOCX' => 'bg-blue-50 text-blue-600',
            'XLS', 'XLSX' => 'bg-emerald-50 text-emerald-600',
            'PNG', 'JPG', 'JPEG' => 'bg-purple-50 text-purple-600',
            default => 'bg-gray-100 text-gray-600',
        };
        return [$ext, $color];
    };
    $messages = $serviceRequest->messages->sortBy('created_at');
    $me = auth()->user();
@endphp

@section('content')
<x-espace.header :back="route('projects.index')" back-label="{{ $me->isAdmin() ? 'Projets' : ($me->isExpert() ? 'Projets assignés' : 'Mes projets') }}"
              :eyebrow="$serviceRequest->reference.' · '.(\App\Models\ServiceRequest::SERVICE_TYPES[$serviceRequest->service_type] ?? $serviceRequest->service_type)"
              :title="$serviceRequest->title">
    <x-slot:actions>
        <x-status-badge :status="$serviceRequest->status" size="lg" />
        @if($canCancel)
            <div x-data="{ open: {{ $errors->has('reason') ? 'true' : 'false' }} }">
                <button type="button" @click="open = true" class="btn-outline !py-2">Annuler la demande</button>
                <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-[70] flex items-center justify-center bg-dark/50 px-4" @keydown.escape.window="open = false">
                    <form method="POST" action="{{ route('projects.cancel', $serviceRequest) }}" @click.outside="open = false"
                          class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl" role="dialog" aria-modal="true" aria-labelledby="cancel-title">
                        @csrf
                        <h2 id="cancel-title" class="text-lg font-semibold text-dark">Annuler cette demande ?</h2>
                        <p class="mt-2 text-sm leading-6 text-muted">L’équipe AgriFish sera prévenue. La demande restera visible dans votre espace, mais ne sera plus traitée.</p>
                        <label for="cancel-reason" class="field-label mt-5">Motif (facultatif)</label>
                        <textarea id="cancel-reason" name="reason" rows="3" maxlength="500" class="field-input" placeholder="Ex : le projet est reporté à la saison prochaine.">{{ old('reason') }}</textarea>
                        @error('reason')<p class="field-error">{{ $message }}</p>@enderror
                        <div class="mt-5 flex justify-end gap-2">
                            <button type="button" @click="open = false" class="btn-outline">Retour</button>
                            <button type="submit" class="btn bg-red-700 text-white hover:bg-red-800">Confirmer l’annulation</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </x-slot:actions>

    <div class="mt-6 grid gap-3 lg:grid-cols-[1.3fr_1fr]">
        <div class="rounded-xl border border-line bg-white p-5">
            <div class="flex items-baseline justify-between text-sm">
                <span class="text-muted">Avancement du projet</span>
                <span class="text-xl font-semibold text-dark">{{ $progress }} %</span>
            </div>
            <div class="mt-3 h-2 overflow-hidden rounded-full bg-cream" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100" aria-label="Avancement">
                <div class="h-full rounded-full bg-primary-500" style="width: {{ $progress }}%"></div>
            </div>
            <p class="mt-3 text-sm text-muted">
                {{ $milestoneStats['validated'] + $milestoneStats['completed'] }} étape(s) validée(s) sur {{ $serviceRequest->milestones->count() }}
                @if($milestoneStats['blocked'])
                    · <span class="font-medium text-red-700">{{ $milestoneStats['blocked'] }} bloquée(s)</span>
                @endif
            </p>
        </div>
        <dl class="grid grid-cols-2 gap-4 rounded-xl border border-line bg-white p-5 text-sm">
            <div>
                <dt class="text-muted">Expert</dt>
                <dd class="mt-0.5 font-medium text-dark">{{ $serviceRequest->assignedExpert?->name ?? 'À désigner' }}</dd>
            </div>
            <div>
                <dt class="text-muted">Client</dt>
                <dd class="mt-0.5 font-medium text-dark">{{ $serviceRequest->client?->name }}</dd>
            </div>
            <div>
                <dt class="text-muted">Créé le</dt>
                <dd class="mt-0.5 font-medium text-dark">{{ $serviceRequest->created_at->format('d/m/Y') }}</dd>
            </div>
            <div>
                <dt class="text-muted">Priorité</dt>
                <dd class="mt-0.5 font-medium text-dark">{{ ['low' => 'Faible', 'normal' => 'Normale', 'high' => 'Haute'][$serviceRequest->priority] ?? $serviceRequest->priority }}</dd>
            </div>
        </dl>
    </div>
</x-espace.header>

@php
    $initialTab = match (true) {
        $errors->has('body') || $errors->has('attachment') || session('status') === 'message-sent' => 'messagerie',
        $errors->has('documents') || $errors->has('documents.*') || in_array(session('status'), ['documents-added', 'document-removed'], true) => 'documents',
        old('milestone_id') !== null || $errors->has('title') || $errors->has('due_date') || $errors->has('deliverables.*') || in_array(session('status'), ['milestone-created', 'milestone-updated', 'milestone-moved', 'milestone-deleted', 'deliverable-removed'], true) => 'etapes',
        default => 'apercu',
    };
    $deliverables = $serviceRequest->milestones->flatMap(fn ($m) => collect($m->deliverable_paths ?? [])->map(fn ($path, $index) => ['milestone' => $m, 'path' => $path, 'index' => $index]));
    $tabs = [
        'apercu' => ['Aperçu', null],
        'etapes' => ['Étapes', $serviceRequest->milestones->count()],
        'documents' => ['Documents', count($serviceRequest->document_paths ?? []) + $deliverables->count()],
        'messagerie' => ['Messagerie', $messages->count()],
        'historique' => ['Historique', null],
    ];
    $nextMilestone = $serviceRequest->milestones->first(fn ($m) => ! in_array($m->status, ['validated', 'completed']));
@endphp

<div x-data="{ tab: @js($initialTab), open(name) { this.tab = name; history.replaceState(null, '', '#' + name) } }"
     x-init="const h = location.hash.slice(1); if (@js(array_keys($tabs)).includes(h)) tab = h"
     @hashchange.window="const h = location.hash.slice(1); if (@js(array_keys($tabs)).includes(h)) tab = h">
    <div class="sticky top-14 z-20 border-b border-line bg-white lg:top-0">
        <nav class="mx-auto flex max-w-6xl gap-1 overflow-x-auto px-3 sm:px-6" aria-label="Sections du projet" role="tablist">
            @foreach($tabs as $key => [$label, $count])
                <a href="#{{ $key }}" role="tab" id="tab-{{ $key }}" @click.prevent="open('{{ $key }}')"
                   :aria-selected="tab === '{{ $key }}'"
                   :class="tab === '{{ $key }}' ? 'border-primary-800 text-dark' : 'border-transparent text-muted hover:text-dark'"
                   class="inline-flex shrink-0 items-center gap-2 border-b-2 px-3 py-3.5 text-sm font-medium transition-colors">
                    {{ $label }}
                    @if($count)<span class="rounded-full bg-cream px-1.5 text-xs text-muted">{{ $count }}</span>@endif
                </a>
            @endforeach
        </nav>
    </div>

    <x-espace.body>
        <div x-show="tab === 'apercu'" role="tabpanel" aria-labelledby="tab-apercu">
            <div class="grid items-start gap-6 lg:grid-cols-[1fr_20rem]">
                <div class="space-y-6">
            @if($serviceRequest->status === 'cancelled')
                <div class="rounded-xl border border-line bg-white p-4 text-sm text-muted">
                    Cette demande a été annulée{{ $serviceRequest->status_changed_at ? ' le '.$serviceRequest->status_changed_at->format('d/m/Y') : '' }}. Pour relancer le projet, <a href="{{ route('requests.create') }}" class="font-medium text-dark underline underline-offset-2">déposez une nouvelle demande</a>.
                </div>
            @endif
            @if($serviceRequest->status_notes)
                <div class="flex gap-3 rounded-xl border border-gold/30 bg-gold/10 p-4 text-sm text-dark">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-gold-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div><span class="font-semibold">Note de l’équipe :</span> {{ $serviceRequest->status_notes }}</div>
                </div>
            @endif

            <div class="card" x-data="{ more: false }">
                <h2 class="text-[17px] font-semibold text-dark">Besoin exprimé</h2>
                <p class="mt-3 whitespace-pre-line text-[15px] leading-7 text-muted" :class="more ? '' : 'line-clamp-4'">{{ $serviceRequest->description }}</p>
                @if(mb_strlen($serviceRequest->description) > 280)
                    <button type="button" @click="more = !more" class="mt-2 text-sm font-medium text-primary-800" x-text="more ? 'Réduire' : 'Lire la suite'"></button>
                @endif
            </div>

                </div>
                <aside class="space-y-4">
                    <div class="card">
                        <h2 class="text-[15px] font-semibold text-dark">Prochaine étape</h2>
                        @if($nextMilestone)
                            <p class="mt-2 font-medium text-dark">{{ $nextMilestone->title }}</p>
                            <p class="mt-1 text-sm text-muted">{{ $nextMilestone->statusLabel() }}{{ $nextMilestone->due_date ? ' · échéance '.$nextMilestone->due_date->translatedFormat('d M Y') : '' }}</p>
                        @elseif($serviceRequest->milestones->isEmpty())
                            <p class="mt-2 text-sm leading-6 text-muted">{{ $canManageMilestones ? 'Planifiez les étapes du projet pour que le client suive l’avancement.' : 'Votre expert va planifier les étapes de votre projet.' }}</p>
                        @else
                            <p class="mt-2 text-sm text-muted">Toutes les étapes sont validées.</p>
                        @endif
                        <a href="#etapes" @click.prevent="open('etapes')" class="link-arrow mt-3 inline-flex text-sm">Voir les étapes</a>
                    </div>
                    <div class="card">
                        <h2 class="text-[15px] font-semibold text-dark">Raccourcis</h2>
                        <ul class="mt-2 space-y-1 text-sm">
                            <li><a href="#documents" @click.prevent="open('documents')" class="flex justify-between rounded-lg px-2 py-1.5 hover:bg-cream"><span>Documents et livrables</span><span class="text-muted">{{ $tabs['documents'][1] }}</span></a></li>
                            <li><a href="#messagerie" @click.prevent="open('messagerie')" class="flex justify-between rounded-lg px-2 py-1.5 hover:bg-cream"><span>Messages</span><span class="text-muted">{{ $messages->count() }}</span></a></li>
                            <li><a href="#historique" @click.prevent="open('historique')" class="flex justify-between rounded-lg px-2 py-1.5 hover:bg-cream"><span>Historique</span><span class="text-muted">{{ $serviceRequest->events->count() }}</span></a></li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>

        <div x-show="tab === 'etapes'" x-cloak role="tabpanel" aria-labelledby="tab-etapes" class="mx-auto max-w-3xl">
            <div class="card">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-[17px] font-semibold text-dark">Étapes du projet</h2>
                    <span class="text-sm text-muted">{{ $serviceRequest->milestones->count() }} étape(s)</span>
                </div>

                <ol class="mt-6">
                    @forelse($serviceRequest->milestones as $milestone)
                        @php
                            $overdue = $milestone->due_date && $milestone->due_date->isPast() && ! in_array($milestone->status, ['validated', 'completed']);
                        @endphp
                        <li class="relative flex gap-4 pb-8 last:pb-0" x-data="{ edit: {{ $errors->any() && old('milestone_id') == $milestone->id ? 'true' : 'false' }} }">
                            @unless($loop->last)
                                <span class="absolute left-[15px] top-9 h-[calc(100%-2.25rem)] w-0.5 bg-line" aria-hidden="true"></span>
                            @endunless
                            <span class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full ring-4 {{ $dotStyles[$milestone->status] ?? $dotStyles['planned'] }}">
                                @if(in_array($milestone->status, ['validated', 'completed']))
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @elseif($milestone->status === 'blocked')
                                    <span class="text-sm font-bold">!</span>
                                @else
                                    <span class="text-xs font-bold">{{ $loop->iteration }}</span>
                                @endif
                            </span>

                            <div class="min-w-0 flex-1 pt-0.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-semibold text-dark">{{ $milestone->title }}</h3>
                                    <x-status-badge :status="$milestone->status" :label="$milestone->statusLabel()" />
                                </div>
                                @if($milestone->description)
                                    <p class="mt-1 text-sm leading-6 text-muted">{{ $milestone->description }}</p>
                                @endif
                                @if($milestone->due_date)
                                    <p class="mt-2 inline-flex items-center gap-1.5 text-xs {{ $overdue ? 'font-medium text-red-700' : 'text-muted' }}">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $overdue ? 'En retard · ' : 'Échéance ' }}{{ $milestone->due_date->translatedFormat('d M Y') }}
                                    </p>
                                @endif

                                @if(! empty($milestone->deliverable_paths))
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach($milestone->deliverable_paths as $index => $path)
                                            @php [$ext, $color] = $fileMeta($path); @endphp
                                            <span class="inline-flex items-center rounded-lg border border-line bg-white text-sm font-medium text-dark transition hover:border-dark/30">
                                                <a href="{{ route('projects.milestones.download', [$milestone, $index]) }}" class="inline-flex items-center gap-2 py-1.5 pl-1.5 pr-3">
                                                    <span class="rounded-md px-1.5 py-0.5 text-[10px] font-bold {{ $color }}">{{ $ext }}</span>
                                                    Livrable {{ $index + 1 }}
                                                    <svg class="h-4 w-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </a>
                                                @if($canManageMilestones)
                                                    <form method="POST" action="{{ route('projects.milestones.deliverables.destroy', [$milestone, $index]) }}" onsubmit="return confirm('Retirer ce livrable ?')" class="border-l border-line">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="flex h-8 w-8 items-center justify-center text-muted hover:text-red-700" aria-label="Retirer le livrable {{ $index + 1 }}">
                                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                @if($canManageMilestones)
                                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2">
                                        <button type="button" @click="edit = !edit" class="inline-flex items-center gap-1 text-sm font-medium text-primary-800 hover:text-primary-dark">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.41-9.41a2 2 0 112.83 2.83L11.83 15H9v-2.83l8.59-8.58z"/></svg>
                                            <span x-text="edit ? 'Annuler' : 'Mettre à jour'"></span>
                                        </button>
                                        @if($serviceRequest->milestones->count() > 1)
                                            <span class="inline-flex items-center gap-1">
                                                @foreach(['up' => ['Monter', 'M5 15l7-7 7 7'], 'down' => ['Descendre', 'M19 9l-7 7-7-7']] as $direction => [$moveLabel, $icon])
                                                    @php $disabled = $direction === 'up' ? $loop->parent->first : $loop->parent->last; @endphp
                                                    <form method="POST" action="{{ route('projects.milestones.move', $milestone) }}">
                                                        @csrf
                                                        <input type="hidden" name="direction" value="{{ $direction }}">
                                                        <button type="submit" @disabled($disabled) title="{{ $moveLabel }}" aria-label="{{ $moveLabel }} l’étape {{ $milestone->title }}"
                                                                class="flex h-7 w-7 items-center justify-center rounded-md border border-line text-muted transition hover:border-dark/30 hover:text-dark disabled:cursor-not-allowed disabled:opacity-40">
                                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                                                        </button>
                                                    </form>
                                                @endforeach
                                            </span>
                                        @endif
                                        <form method="POST" action="{{ route('projects.milestones.destroy', $milestone) }}" onsubmit="return confirm('Supprimer l’étape « {{ addslashes($milestone->title) }} » et ses livrables ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-800">Supprimer</button>
                                        </form>
                                    </div>

                                    <form x-show="edit" x-cloak x-transition method="POST" action="{{ route('projects.milestones.update', $milestone) }}" enctype="multipart/form-data"
                                          class="mt-3 space-y-4 rounded-xl bg-cream p-4">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="milestone_id" value="{{ $milestone->id }}">
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div>
                                                <label for="milestone-title-{{ $milestone->id }}" class="field-label">Titre</label>
                                                <input id="milestone-title-{{ $milestone->id }}" type="text" name="title" required maxlength="150" value="{{ $milestone->title }}" class="field-input">
                                            </div>
                                            <div>
                                                <label for="milestone-status-{{ $milestone->id }}" class="field-label">Statut</label>
                                                <select id="milestone-status-{{ $milestone->id }}" name="status" class="field-input">
                                                    @foreach($milestoneStatuses as $value => $label)
                                                        <option value="{{ $value }}" @selected($milestone->status === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="milestone-description-{{ $milestone->id }}" class="field-label">Description</label>
                                            <textarea id="milestone-description-{{ $milestone->id }}" name="description" rows="3" class="field-input">{{ $milestone->description }}</textarea>
                                        </div>
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div>
                                                <label for="milestone-due-date-{{ $milestone->id }}" class="field-label">Échéance</label>
                                                <input id="milestone-due-date-{{ $milestone->id }}" type="date" name="due_date" value="{{ optional($milestone->due_date)->format('Y-m-d') }}" class="field-input">
                                            </div>
                                            <div>
                                                <span class="field-label">Ajouter des livrables</span>
                                                <x-file-input name="deliverables[]" :id="'milestone-deliverables-'.$milestone->id" multiple compact hint="5 Mo max" />
                                            </div>
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" @click="edit = false" class="btn-outline">Annuler</button>
                                            <button type="submit" class="btn-primary">Enregistrer</button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="rounded-xl border border-dashed border-line px-4 py-8 text-center text-sm text-muted">
                            {{ $canManageMilestones ? 'Aucune étape pour l’instant : ajoutez la première ci-dessous.' : 'Votre expert va bientôt planifier les étapes de votre projet.' }}
                        </li>
                    @endforelse
                </ol>

                @if($canManageMilestones)
                    <div x-data="{ open: {{ $errors->any() && ! old('milestone_id') && old('title') !== null ? 'true' : 'false' }} }" class="mt-6 border-t border-line pt-5">
                        <button type="button" x-show="!open" @click="open = true" class="btn-outline w-full border-dashed">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Ajouter une étape
                        </button>
                        <form x-show="open" x-cloak x-transition method="POST" action="{{ route('projects.milestones.store', $serviceRequest) }}" class="space-y-4">
                            @csrf
                            <h3 class="font-semibold text-dark">Nouvelle étape</h3>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="new-milestone-title" class="field-label">Titre *</label>
                                    <input id="new-milestone-title" type="text" name="title" required maxlength="150" value="{{ old('milestone_id') ? '' : old('title') }}" class="field-input" placeholder="Ex : Étude initiale">
                                </div>
                                <div>
                                    <label for="new-milestone-status" class="field-label">Statut</label>
                                    <select id="new-milestone-status" name="status" class="field-input">
                                        @foreach($milestoneStatuses as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="new-milestone-description" class="field-label">Description</label>
                                <textarea id="new-milestone-description" name="description" rows="3" class="field-input" placeholder="Décrivez l’étape et le livrable attendu."></textarea>
                            </div>
                            <div class="sm:w-1/2">
                                <label for="new-milestone-due-date" class="field-label">Échéance</label>
                                <input id="new-milestone-due-date" type="date" name="due_date" class="field-input">
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="open = false" class="btn-outline">Annuler</button>
                                <button type="submit" class="btn-primary">Créer l’étape</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <div x-show="tab === 'documents'" x-cloak role="tabpanel" aria-labelledby="tab-documents" class="grid items-start gap-6 lg:grid-cols-2">
            <div class="card">
                <h2 class="text-[17px] font-semibold text-dark">Documents de la demande</h2>
                <ul class="mt-4 divide-y divide-line">
                    @forelse($serviceRequest->document_paths ?? [] as $index => $path)
                        @php [$ext, $color] = $fileMeta($path); @endphp
                        <li class="flex items-center gap-2">
                            <a href="{{ route('projects.documents.download', [$serviceRequest, $index]) }}" class="group flex flex-1 items-center gap-3 py-2.5">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-[11px] font-bold {{ $color }}">{{ $ext }}</span>
                                <span class="flex-1 text-sm font-medium text-dark group-hover:underline">Document {{ $index + 1 }}</span>
                                <svg class="h-5 w-5 text-muted group-hover:text-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                            @if($canRemoveDocuments)
                                <form method="POST" action="{{ route('projects.documents.destroy', [$serviceRequest, $index]) }}" onsubmit="return confirm('Retirer ce document de la demande ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-md text-muted hover:bg-red-50 hover:text-red-700" aria-label="Retirer le document {{ $index + 1 }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.87 12.14A2 2 0 0116.14 21H7.86a2 2 0 01-1.99-1.86L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </li>
                    @empty
                        <li class="py-3 text-sm text-muted">Aucun document joint à la demande.</li>
                    @endforelse
                </ul>
                @if($canAddDocuments && count($serviceRequest->document_paths ?? []) < 10)
                    <form method="POST" action="{{ route('projects.documents.store', $serviceRequest) }}" enctype="multipart/form-data"
                          x-data="{ open: {{ $errors->has('documents') || $errors->has('documents.*') ? 'true' : 'false' }} }" class="mt-4 border-t border-line pt-4">
                        @csrf
                        <button type="button" x-show="!open" @click="open = true" class="text-sm font-medium text-primary-800 hover:text-primary-dark">+ Ajouter des documents</button>
                        <div x-show="open" x-cloak class="space-y-3">
                            <x-file-input name="documents[]" id="add-documents" multiple compact hint="PDF, Word, Excel ou image, 5 Mo max" />
                            @error('documents')<p class="field-error">{{ $message }}</p>@enderror
                            @error('documents.*')<p class="field-error">{{ $message }}</p>@enderror
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="open = false" class="btn-outline !py-2">Annuler</button>
                                <button type="submit" class="btn-primary !py-2">Envoyer</button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>

            <div class="card">
                <h2 class="text-[17px] font-semibold text-dark">Livrables de l’expert</h2>
                <ul class="mt-4 divide-y divide-line">
                    @forelse($deliverables as $deliverable)
                        @php [$ext, $color] = $fileMeta($deliverable['path']); @endphp
                        <li>
                            <a href="{{ route('projects.milestones.download', [$deliverable['milestone'], $deliverable['index']]) }}" class="group flex items-center gap-3 py-2.5">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-[11px] font-bold {{ $color }}">{{ $ext }}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-medium text-dark group-hover:underline">Livrable {{ $deliverable['index'] + 1 }}</span>
                                    <span class="block truncate text-xs text-muted">Étape : {{ $deliverable['milestone']->title }}</span>
                                </span>
                                <svg class="h-5 w-5 text-muted group-hover:text-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-muted">Les livrables apparaîtront ici dès que l’expert les déposera sur une étape.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div x-show="tab === 'messagerie'" x-cloak role="tabpanel" aria-labelledby="tab-messagerie" class="mx-auto max-w-3xl">
            <div class="card flex flex-col !p-0">
                <div class="border-b border-line px-5 py-4 sm:px-6">
                    <h2 class="text-[17px] font-semibold text-dark">Messagerie</h2>
                    <p class="text-xs text-muted">Échanges entre le client et l’expert du projet</p>
                </div>

                <div x-data x-init="$el.scrollTop = $el.scrollHeight" class="max-h-[28rem] space-y-4 overflow-y-auto bg-cream/50 px-4 py-5 sm:px-6">
                    @forelse($messages as $message)
                        @php $mine = $message->sender_id === $me->id; @endphp
                        <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%]">
                                <div class="mb-1 flex items-center gap-2 text-[11px] text-muted {{ $mine ? 'justify-end' : '' }}">
                                    <span class="font-medium text-dark">{{ $mine ? 'Vous' : ($message->sender?->name ?? 'Utilisateur supprimé') }}</span>
                                    <time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->format('d/m H:i') }}</time>
                                </div>
                                <div class="rounded-2xl px-4 py-3 text-sm leading-6 {{ $mine ? 'rounded-br-md bg-primary-800 text-white' : 'rounded-bl-md bg-white text-dark ring-1 ring-line' }}">
                                    <p class="whitespace-pre-line break-words">{{ $message->body }}</p>
                                    @if($message->attachment_path)
                                        <a href="{{ route('projects.messages.attachment', [$serviceRequest, $message]) }}"
                                           class="mt-2 inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-semibold {{ $mine ? 'bg-white/15 text-white hover:bg-white/25' : 'bg-cream text-primary-800 hover:bg-sand' }}">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.17 7l-6.58 6.59a2 2 0 102.83 2.83l6.41-6.59a4 4 0 00-5.66-5.66l-6.4 6.58a6 6 0 108.49 8.49L20.5 13"/></svg>
                                            Pièce jointe ({{ $fileMeta($message->attachment_path)[0] }})
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-muted">Aucun message. Posez votre première question ici.</p>
                    @endforelse
                </div>

                @if($serviceRequest->status === 'cancelled')
                    <p class="border-t border-line p-4 text-sm text-muted sm:p-5">La messagerie est fermée pour une demande annulée.</p>
                @else
                <form method="POST" action="{{ route('projects.messages.store', $serviceRequest) }}" enctype="multipart/form-data"
                      x-data="{ sending: false }" @submit="sending = true" class="space-y-3 border-t border-line p-4 sm:p-5">
                    @csrf
                    <label for="new-project-message" class="sr-only">Votre message</label>
                    <textarea id="new-project-message" name="body" rows="3" required minlength="2" maxlength="4000"
                              class="field-input @error('body') border-red-400 @enderror" placeholder="Écrivez votre message…">{{ old('body') }}</textarea>
                    @error('body')<p class="field-error">{{ $message }}</p>@enderror
                    @error('attachment')<p class="field-error">{{ $message }}</p>@enderror
                    <x-file-input name="attachment" id="new-project-attachment" compact />
                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary" :disabled="sending">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6l6 6-6 6"/></svg>
                            <span x-text="sending ? 'Envoi…' : 'Envoyer'">Envoyer</span>
                        </button>
                    </div>
                </form>
                @endif
            </div>

        </div>

        <div x-show="tab === 'historique'" x-cloak role="tabpanel" aria-labelledby="tab-historique" class="mx-auto max-w-3xl">
            <div class="card">
                <h2 class="text-[17px] font-semibold text-dark">Historique</h2>
                <ol class="mt-4 space-y-4">
                    @forelse($serviceRequest->events->take(12) as $event)
                        <li class="flex gap-3 text-sm">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $event->type === 'cancelled' ? 'bg-red-500' : ($event->type === 'created' ? 'bg-primary-500' : 'bg-gold') }}" aria-hidden="true"></span>
                            <div class="min-w-0">
                                <p class="font-medium text-dark">{{ $event->summary() }}</p>
                                @if($event->type === 'cancelled' && $event->details)
                                    <p class="mt-0.5 text-muted">« {{ $event->details }} »</p>
                                @endif
                                <p class="mt-0.5 text-xs text-muted">
                                    <time datetime="{{ $event->created_at->toIso8601String() }}">{{ $event->created_at->translatedFormat('d M Y à H:i') }}</time>
                                    @if($event->user) · {{ $event->user->id === $me->id ? 'vous' : $event->user->name }}@endif
                                </p>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-muted">Demande déposée le {{ $serviceRequest->created_at->translatedFormat('d M Y') }}.</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </x-espace.body>
</div>
@endsection
