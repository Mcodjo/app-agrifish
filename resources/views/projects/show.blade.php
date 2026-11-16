@extends('layouts.app')

@section('title', $serviceRequest->reference . ' - Projet')
@section('meta_description', 'Suivi du projet ' . $serviceRequest->reference . ' : jalons, documents et messagerie.')

@php
    $milestoneStatuses = ['planned' => 'Planifié', 'in_progress' => 'En cours', 'validated' => 'Validé', 'blocked' => 'Bloqué', 'completed' => 'Terminé'];
    $dotStyles = [
        'planned' => 'bg-white ring-gray-300 text-gray-400',
        'in_progress' => 'bg-primary ring-primary-200 text-white',
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
<x-app-header :back="route('dashboard')" back-label="Tableau de bord"
              :eyebrow="$serviceRequest->reference.' · '.(\App\Models\ServiceRequest::SERVICE_TYPES[$serviceRequest->service_type] ?? $serviceRequest->service_type)"
              :title="$serviceRequest->title">
    <x-slot:actions>
        <x-status-badge :status="$serviceRequest->status" size="lg" class="!bg-white/95" />
    </x-slot:actions>

    <div class="mt-8 grid gap-4 lg:grid-cols-[1.4fr_1fr]">
        <div class="rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur">
            <div class="flex items-center justify-between text-sm">
                <span class="text-white/75">Avancement du projet</span>
                <span class="text-lg font-extrabold text-gold-light">{{ $progress }}%</span>
            </div>
            <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-white/15" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                <div class="h-full rounded-full bg-gold transition-all" style="width: {{ $progress }}%"></div>
            </div>
            <p class="mt-3 text-xs text-white/60">
                {{ $milestoneStats['validated'] + $milestoneStats['completed'] }} sur {{ $serviceRequest->milestones->count() }} jalons validés
                @if($milestoneStats['blocked'])
                    · <span class="font-semibold text-red-300">{{ $milestoneStats['blocked'] }} bloqué(s)</span>
                @endif
            </p>
        </div>
        <dl class="grid grid-cols-2 gap-4 rounded-2xl border border-white/10 bg-white/10 p-5 text-sm backdrop-blur">
            <div>
                <dt class="text-white/60">Expert</dt>
                <dd class="mt-0.5 font-semibold">{{ $serviceRequest->assignedExpert?->name ?? 'À assigner' }}</dd>
            </div>
            <div>
                <dt class="text-white/60">Client</dt>
                <dd class="mt-0.5 font-semibold">{{ $serviceRequest->client?->name }}</dd>
            </div>
            <div>
                <dt class="text-white/60">Créé le</dt>
                <dd class="mt-0.5 font-semibold">{{ $serviceRequest->created_at->format('d/m/Y') }}</dd>
            </div>
            <div>
                <dt class="text-white/60">Priorité</dt>
                <dd class="mt-0.5 font-semibold">{{ ['low' => 'Faible', 'normal' => 'Normale', 'high' => 'Haute'][$serviceRequest->priority] ?? $serviceRequest->priority }}</dd>
            </div>
        </dl>
    </div>
</x-app-header>

<section class="bg-cream py-10 lg:py-14">
    <div class="max-w-content grid items-start gap-6 px-4 lg:grid-cols-[1.25fr_1fr] lg:px-8">

        {{-- Colonne gauche : besoin + timeline --}}
        <div class="space-y-6">
            @if($serviceRequest->status_notes)
                <div class="flex gap-3 rounded-2xl border border-gold/30 bg-gold/10 p-4 text-sm text-dark">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-gold-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div><span class="font-semibold">Note de l’équipe :</span> {{ $serviceRequest->status_notes }}</div>
                </div>
            @endif

            <div class="card" x-data="{ more: false }">
                <h2 class="text-lg font-bold text-dark">Besoin exprimé</h2>
                <p class="mt-3 whitespace-pre-line text-sm leading-7 text-gray-600" :class="more ? '' : 'line-clamp-4'">{{ $serviceRequest->description }}</p>
                @if(mb_strlen($serviceRequest->description) > 280)
                    <button type="button" @click="more = !more" class="mt-2 text-sm font-semibold text-primary" x-text="more ? 'Réduire' : 'Lire la suite'"></button>
                @endif
            </div>

            <div class="card">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-lg font-bold text-dark">Étapes du projet</h2>
                    <span class="text-sm text-gray-500">{{ $serviceRequest->milestones->count() }} jalon(s)</span>
                </div>

                <ol class="mt-6">
                    @forelse($serviceRequest->milestones as $milestone)
                        @php
                            $overdue = $milestone->due_date && $milestone->due_date->isPast() && ! in_array($milestone->status, ['validated', 'completed']);
                        @endphp
                        <li class="relative flex gap-4 pb-8 last:pb-0" x-data="{ edit: {{ $errors->any() && old('milestone_id') == $milestone->id ? 'true' : 'false' }} }">
                            @unless($loop->last)
                                <span class="absolute left-[15px] top-9 h-[calc(100%-2.25rem)] w-0.5 bg-gray-200" aria-hidden="true"></span>
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
                                    <h3 class="font-bold text-dark">{{ $milestone->title }}</h3>
                                    <x-status-badge :status="$milestone->status" :label="$milestone->statusLabel()" />
                                </div>
                                @if($milestone->description)
                                    <p class="mt-1 text-sm leading-6 text-gray-600">{{ $milestone->description }}</p>
                                @endif
                                @if($milestone->due_date)
                                    <p class="mt-2 inline-flex items-center gap-1.5 text-xs {{ $overdue ? 'font-semibold text-red-600' : 'text-gray-500' }}">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $overdue ? 'En retard · ' : 'Échéance ' }}{{ $milestone->due_date->translatedFormat('d M Y') }}
                                    </p>
                                @endif

                                @if(! empty($milestone->deliverable_paths))
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach($milestone->deliverable_paths as $index => $path)
                                            @php [$ext, $color] = $fileMeta($path); @endphp
                                            <a href="{{ route('projects.milestones.download', [$milestone, $index]) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white py-1.5 pl-1.5 pr-3 text-sm font-medium text-gray-700 transition hover:border-primary hover:text-primary">
                                                <span class="rounded-md px-1.5 py-0.5 text-[10px] font-bold {{ $color }}">{{ $ext }}</span>
                                                Livrable {{ $index + 1 }}
                                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                @if($canManageMilestones)
                                    <button type="button" @click="edit = !edit" class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-primary hover:text-primary-dark">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.41-9.41a2 2 0 112.83 2.83L11.83 15H9v-2.83l8.59-8.58z"/></svg>
                                        <span x-text="edit ? 'Annuler' : 'Mettre à jour'"></span>
                                    </button>

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
                        <li class="rounded-xl border-2 border-dashed border-gray-200 px-4 py-8 text-center text-sm text-gray-500">
                            {{ $canManageMilestones ? 'Aucun jalon pour l’instant : ajoutez la première étape ci-dessous.' : 'Votre expert va bientôt planifier les étapes de votre projet.' }}
                        </li>
                    @endforelse
                </ol>

                @if($canManageMilestones)
                    <div x-data="{ open: {{ $errors->any() && ! old('milestone_id') && old('title') !== null ? 'true' : 'false' }} }" class="mt-6 border-t border-gray-100 pt-5">
                        <button type="button" x-show="!open" @click="open = true" class="btn-outline w-full border-dashed">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Ajouter un jalon
                        </button>
                        <form x-show="open" x-cloak x-transition method="POST" action="{{ route('projects.milestones.store', $serviceRequest) }}" class="space-y-4">
                            @csrf
                            <h3 class="font-bold text-dark">Nouveau jalon</h3>
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
                                <button type="submit" class="btn-primary">Créer le jalon</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Colonne droite : documents + messagerie --}}
        <div class="space-y-6 lg:sticky lg:top-28">
            <div class="card">
                <h2 class="text-lg font-bold text-dark">Documents de la demande</h2>
                <ul class="mt-4 divide-y divide-gray-100">
                    @forelse($serviceRequest->document_paths ?? [] as $index => $path)
                        @php [$ext, $color] = $fileMeta($path); @endphp
                        <li>
                            <a href="{{ route('projects.documents.download', [$serviceRequest, $index]) }}" class="group flex items-center gap-3 py-2.5">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-[11px] font-bold {{ $color }}">{{ $ext }}</span>
                                <span class="flex-1 text-sm font-medium text-gray-700 group-hover:text-primary">Document {{ $index + 1 }}</span>
                                <svg class="h-5 w-5 text-gray-400 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">Aucun document joint à la demande.</li>
                    @endforelse
                </ul>
            </div>

            <div class="card flex flex-col !p-0">
                <div class="border-b border-gray-100 px-5 py-4 sm:px-6">
                    <h2 class="text-lg font-bold text-dark">Messagerie</h2>
                    <p class="text-xs text-gray-500">Échanges entre le client et l’expert du projet</p>
                </div>

                <div x-data x-init="$el.scrollTop = $el.scrollHeight" class="max-h-[28rem] space-y-4 overflow-y-auto bg-gray-50/60 px-4 py-5 sm:px-6">
                    @forelse($messages as $message)
                        @php $mine = $message->sender_id === $me->id; @endphp
                        <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%]">
                                <div class="mb-1 flex items-center gap-2 text-[11px] text-gray-500 {{ $mine ? 'justify-end' : '' }}">
                                    <span class="font-semibold text-gray-700">{{ $mine ? 'Vous' : ($message->sender?->name ?? 'Utilisateur supprimé') }}</span>
                                    <time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->format('d/m H:i') }}</time>
                                </div>
                                <div class="rounded-2xl px-4 py-3 text-sm leading-6 {{ $mine ? 'rounded-br-md bg-primary text-white' : 'rounded-bl-md bg-white text-gray-700 ring-1 ring-gray-100' }}">
                                    <p class="whitespace-pre-line break-words">{{ $message->body }}</p>
                                    @if($message->attachment_path)
                                        <a href="{{ route('projects.messages.attachment', [$serviceRequest, $message]) }}"
                                           class="mt-2 inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-semibold {{ $mine ? 'bg-white/15 text-white hover:bg-white/25' : 'bg-cream text-primary hover:bg-primary-50' }}">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.17 7l-6.58 6.59a2 2 0 102.83 2.83l6.41-6.59a4 4 0 00-5.66-5.66l-6.4 6.58a6 6 0 108.49 8.49L20.5 13"/></svg>
                                            Pièce jointe ({{ $fileMeta($message->attachment_path)[0] }})
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-gray-500">Aucun message. Posez votre première question ici.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('projects.messages.store', $serviceRequest) }}" enctype="multipart/form-data"
                      x-data="{ sending: false }" @submit="sending = true" class="space-y-3 border-t border-gray-100 p-4 sm:p-5">
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
            </div>
        </div>
    </div>
</section>
@endsection
