@extends('layouts.app')

@section('title', 'Admin - Demandes')
@section('meta_description', 'Interface admin AgriFish pour suivre, assigner et mettre à jour les demandes des clients.')

@section('content')
<section class="relative overflow-hidden bg-primary-dark text-white">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 36px 36px;"></div>
    <div class="max-w-content mx-auto px-4 lg:px-8 pt-20 pb-20 lg:pt-28 lg:pb-24 relative z-10">
        <div class="max-w-3xl">
            <p class="text-sm font-bold uppercase tracking-[0.24em] text-gold-light">Admin</p>
            <h1 class="mt-4 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-7xl">Liste des demandes et assignation.</h1>
            <p class="mt-6 text-lg leading-8 text-white/78 lg:text-xl">Attribuez une demande à un expert, mettez à jour son statut et gardez un suivi clair.</p>
        </div>
    </div>
</section>

<section class="bg-cream py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8 space-y-6">
        @foreach($requests as $requestItem)
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr] lg:items-start">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-2xl font-extrabold text-dark">{{ $requestItem->reference }}</h2>
                            <span class="rounded-full bg-primary/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-primary">{{ str_replace('_', ' ', $requestItem->status) }}</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Client: {{ $requestItem->client->name }} - {{ $requestItem->client->email }}</p>
                        <p class="mt-4 text-lg font-bold text-dark">{{ $requestItem->title }}</p>
                        <p class="mt-3 text-gray-600 leading-7">{{ $requestItem->description }}</p>

                        <div class="mt-5 grid gap-4 sm:grid-cols-3 text-sm">
                            <div class="rounded-2xl bg-cream p-4">
                                <div class="font-bold text-dark">Service</div>
                                <div class="mt-1 text-gray-600">{{ $requestItem->service_type }}</div>
                            </div>
                            <div class="rounded-2xl bg-cream p-4">
                                <div class="font-bold text-dark">Priorité</div>
                                <div class="mt-1 text-gray-600">{{ $requestItem->priority }}</div>
                            </div>
                            <div class="rounded-2xl bg-cream p-4">
                                <div class="font-bold text-dark">Expert</div>
                                <div class="mt-1 text-gray-600">{{ $requestItem->assignedExpert?->name ?? 'Non assigné' }}</div>
                            </div>
                        </div>

                        @if(! empty($requestItem->document_paths))
                            <div class="mt-5">
                                <div class="text-sm font-bold uppercase tracking-[0.18em] text-gold">Documents</div>
                                <div class="mt-3 flex flex-wrap gap-3">
                                    @foreach($requestItem->document_paths as $path)
                                        <a href="{{ asset('storage/'.$path) }}" target="_blank" class="rounded-xl border border-gray-200 px-4 py-2 text-sm text-primary hover:bg-primary hover:text-white">Voir le fichier</a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('admin.requests.update', $requestItem) }}" class="rounded-3xl bg-cream p-5 space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700" for="status-{{ $requestItem->id }}">Statut</label>
                            <select id="status-{{ $requestItem->id }}" name="status" class="w-full rounded-xl border-gray-200 bg-white px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                                @foreach([
                                    'new' => 'Nouvelle',
                                    'reviewing' => 'En revue',
                                    'assigned' => 'Assignée',
                                    'in_progress' => 'En cours',
                                    'waiting_client' => 'En attente client',
                                    'completed' => 'Terminée',
                                    'closed' => 'Clôturée',
                                ] as $value => $label)
                                    <option value="{{ $value }}" @selected($requestItem->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700" for="expert-{{ $requestItem->id }}">Expert</label>
                            <select id="expert-{{ $requestItem->id }}" name="assigned_expert_id" class="w-full rounded-xl border-gray-200 bg-white px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                                <option value="">-- Non assigné --</option>
                                @foreach($experts as $expert)
                                    <option value="{{ $expert->id }}" @selected((string) $requestItem->assigned_expert_id === (string) $expert->id)>{{ $expert->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700" for="notes-{{ $requestItem->id }}">Note de statut</label>
                            <textarea id="notes-{{ $requestItem->id }}" name="status_notes" rows="4" class="w-full rounded-xl border-gray-200 bg-white px-4 py-3 text-sm focus:border-primary focus:ring-primary">{{ $requestItem->status_notes }}</textarea>
                        </div>

                        @if(session('status') === 'request-updated')
                            <div class="rounded-2xl border border-green-200 bg-green-50 p-3 text-sm font-medium text-green-700">Demande mise à jour.</div>
                        @endif

                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-5 py-3 font-bold text-white hover:bg-primary-dark">Enregistrer</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection
