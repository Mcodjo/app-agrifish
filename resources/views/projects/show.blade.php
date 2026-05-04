@extends('layouts.app')

@section('title', $serviceRequest->reference . ' - Projet')
@section('meta_description', 'Timeline de projet, documents et messagerie pour ' . $serviceRequest->reference)

@section('content')
<section class="relative overflow-hidden bg-primary-dark text-white">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 36px 36px;"></div>
    <div class="max-w-content mx-auto px-4 lg:px-8 pt-20 pb-20 lg:pt-28 lg:pb-24 relative z-10">
        <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-end">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.24em] text-gold-light">Projet client</p>
                <h1 class="mt-4 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-7xl">{{ $serviceRequest->reference }}</h1>
                <p class="mt-6 max-w-3xl text-lg leading-8 text-white/78 lg:text-xl">{{ $serviceRequest->title }}</p>
            </div>
            <div class="rounded-3xl border border-white/15 bg-white/10 p-6 backdrop-blur">
                <div class="flex items-center justify-between text-sm text-white/70">
                    <span>Progression</span>
                    <span class="font-bold text-gold-light">{{ $progress }}%</span>
                </div>
                <div class="mt-3 h-3 rounded-full bg-white/15 overflow-hidden">
                    <div class="h-full rounded-full bg-gold" style="width: {{ $progress }}%"></div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-white/75">
                    <div>Statut: <strong class="text-white">{{ str_replace('_', ' ', $serviceRequest->status) }}</strong></div>
                    <div>Expert: <strong class="text-white">{{ $serviceRequest->assignedExpert?->name ?? 'Non assigné' }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-cream py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8 space-y-8">
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <p class="text-sm font-bold uppercase tracking-[0.2em] text-gold">Jalons</p>
                <div class="mt-4 space-y-3 text-sm text-gray-600">
                    @foreach($milestoneStats as $label => $count)
                        <div class="flex items-center justify-between rounded-2xl bg-cream px-4 py-3">
                            <span>{{ str_replace('_', ' ', $label) }}</span>
                            <strong class="text-dark">{{ $count }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100 lg:col-span-2">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.2em] text-gold">Documents</p>
                        <h2 class="mt-2 text-2xl font-extrabold text-dark">Rapports, études et contrats</h2>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap gap-3">
                    @forelse($serviceRequest->document_paths ?? [] as $index => $path)
                        <a href="{{ route('projects.documents.download', [$serviceRequest, $index]) }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-primary transition hover:bg-primary hover:text-white">Télécharger le document {{ $index + 1 }}</a>
                    @empty
                        <p class="text-sm text-gray-500">Aucun document disponible pour le moment.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr_0.95fr]">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.2em] text-gold">Timeline</p>
                        <h2 class="mt-2 text-2xl font-extrabold text-dark">Suivi des jalons</h2>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse($serviceRequest->milestones as $milestone)
                        <div class="rounded-3xl border border-gray-100 bg-cream p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-lg font-bold text-dark">{{ $milestone->title }}</h3>
                                    <p class="mt-1 text-sm text-gray-600">{{ $milestone->description }}</p>
                                </div>
                                <span class="inline-flex rounded-full bg-primary/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-primary">{{ $milestone->statusLabel() }}</span>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-500">
                                @if($milestone->due_date)
                                    <span class="rounded-full bg-white px-3 py-1">Échéance: {{ $milestone->due_date->format('d/m/Y') }}</span>
                                @endif
                            </div>

                            @if(! empty($milestone->deliverable_paths))
                                <div class="mt-4 flex flex-wrap gap-3">
                                    @foreach($milestone->deliverable_paths as $index => $path)
                                        <a href="{{ route('projects.milestones.download', [$milestone, $index]) }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-primary hover:bg-primary hover:text-white">Livrable {{ $index + 1 }}</a>
                                    @endforeach
                                </div>
                            @endif

                            @if(auth()->user()->isAdmin() || auth()->user()->isExpert())
                                <form method="POST" action="{{ route('projects.milestones.update', $milestone) }}" enctype="multipart/form-data" class="mt-5 space-y-4 rounded-2xl bg-white p-4 ring-1 ring-gray-100">
                                    @csrf
                                    @method('PATCH')
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label for="milestone-title-{{ $milestone->id }}" class="mb-1 block text-sm font-medium text-gray-700">Titre</label>
                                            <input id="milestone-title-{{ $milestone->id }}" type="text" name="title" value="{{ $milestone->title }}" class="w-full rounded-xl border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                                        </div>
                                        <div>
                                            <label for="milestone-status-{{ $milestone->id }}" class="mb-1 block text-sm font-medium text-gray-700">Statut</label>
                                            <select id="milestone-status-{{ $milestone->id }}" name="status" class="w-full rounded-xl border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                                                @foreach(['planned' => 'Planifié', 'in_progress' => 'En cours', 'validated' => 'Validé', 'blocked' => 'Bloqué', 'completed' => 'Terminé'] as $value => $label)
                                                    <option value="{{ $value }}" @selected($milestone->status === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="milestone-description-{{ $milestone->id }}" class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                                        <textarea id="milestone-description-{{ $milestone->id }}" name="description" rows="3" class="w-full rounded-xl border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-primary">{{ $milestone->description }}</textarea>
                                    </div>
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label for="milestone-due-date-{{ $milestone->id }}" class="mb-1 block text-sm font-medium text-gray-700">Échéance</label>
                                            <input id="milestone-due-date-{{ $milestone->id }}" type="date" name="due_date" value="{{ optional($milestone->due_date)->format('Y-m-d') }}" class="w-full rounded-xl border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                                        </div>
                                        <div>
                                            <label for="milestone-deliverables-{{ $milestone->id }}" class="mb-1 block text-sm font-medium text-gray-700">Livrables</label>
                                            <input id="milestone-deliverables-{{ $milestone->id }}" type="file" name="deliverables[]" multiple class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-xl file:border-0 file:bg-primary file:px-4 file:py-3 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-dark">
                                        </div>
                                    </div>
                                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-3 font-bold text-white hover:bg-primary-dark">Mettre à jour le jalon</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Aucun jalon n’a encore été créé.</p>
                    @endforelse
                </div>

                @if(auth()->user()->isAdmin() || auth()->user()->isExpert())
                    <form method="POST" action="{{ route('projects.milestones.store', $serviceRequest) }}" class="mt-6 space-y-4 rounded-3xl bg-cream p-5">
                        @csrf
                        <h3 class="text-lg font-bold text-dark">Ajouter un jalon</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="new-milestone-title" class="mb-1 block text-sm font-medium text-gray-700">Titre</label>
                                <input id="new-milestone-title" type="text" name="title" class="w-full rounded-xl border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-primary" placeholder="Ex: Étude initiale">
                            </div>
                            <div>
                                <label for="new-milestone-status" class="mb-1 block text-sm font-medium text-gray-700">Statut</label>
                                <select id="new-milestone-status" name="status" class="w-full rounded-xl border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                                    <option value="planned">Planifié</option>
                                    <option value="in_progress">En cours</option>
                                    <option value="validated">Validé</option>
                                    <option value="blocked">Bloqué</option>
                                    <option value="completed">Terminé</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label for="new-milestone-description" class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="new-milestone-description" name="description" rows="3" class="w-full rounded-xl border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-primary" placeholder="Décrivez l’étape et le livrable attendu."></textarea>
                        </div>
                        <div>
                            <label for="new-milestone-due-date" class="mb-1 block text-sm font-medium text-gray-700">Échéance</label>
                            <input id="new-milestone-due-date" type="date" name="due_date" class="w-full rounded-xl border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-3 font-bold text-white hover:bg-primary-dark">Créer le jalon</button>
                    </form>
                @endif
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-gold">Messagerie interne</p>
                    <h2 class="mt-2 text-2xl font-extrabold text-dark">Client et expert</h2>
                </div>

                <div class="mt-6 space-y-4 max-h-[40rem] overflow-auto pr-1">
                    @forelse($serviceRequest->messages as $message)
                        <div class="rounded-3xl {{ $message->sender_id === auth()->id() ? 'bg-primary/10 ml-6' : 'bg-cream mr-6' }} p-4">
                            <div class="flex items-center justify-between gap-4 text-xs text-gray-500">
                                <strong class="text-dark">{{ $message->sender->name }}</strong>
                                <span>{{ $message->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="mt-3 text-sm leading-7 text-gray-700 whitespace-pre-line">{{ $message->body }}</p>
                            @if($message->attachment_path)
                                <a href="{{ asset('storage/'.$message->attachment_path) }}" target="_blank" class="mt-3 inline-flex text-sm font-semibold text-primary">Pièce jointe</a>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Aucun message pour le moment.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('projects.messages.store', $serviceRequest) }}" enctype="multipart/form-data" class="mt-6 space-y-4 rounded-3xl bg-cream p-5">
                    @csrf
                    <div>
                        <label for="new-project-message" class="mb-1 block text-sm font-medium text-gray-700">Nouveau message</label>
                        <textarea id="new-project-message" name="body" rows="4" class="w-full rounded-xl border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-primary" placeholder="Écrivez votre message..."></textarea>
                    </div>
                    <div>
                        <label for="new-project-attachment" class="mb-1 block text-sm font-medium text-gray-700">Pièce jointe</label>
                        <input id="new-project-attachment" type="file" name="attachment" class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-xl file:border-0 file:bg-primary file:px-4 file:py-3 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-dark">
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-3 font-bold text-white hover:bg-primary-dark">Envoyer</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
