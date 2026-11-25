@extends('layouts.espace')

@section('title', 'Utilisateurs')

@section('content')
<x-espace.header :back="route('dashboard')" back-label="Vue d’ensemble" eyebrow="Administration"
              title="Utilisateurs"
              subtitle="Clients, experts et administrateurs de la plateforme. Créez les comptes de l’équipe et gérez les accès.">
    <x-slot:actions>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            Nouveau compte
        </a>
    </x-slot:actions>
</x-espace.header>

<section class="mx-auto max-w-6xl px-5 py-8 sm:px-8 lg:py-10">
    <div class="space-y-4">
        @if($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="rounded-xl border border-line bg-white p-4">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col gap-3 sm:flex-row">
                @if($currentRole)<input type="hidden" name="role" value="{{ $currentRole }}">@endif
                <label for="q" class="sr-only">Rechercher</label>
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="q" type="search" name="q" value="{{ $search }}" placeholder="Nom ou e-mail…" class="field-input pl-11">
                </div>
                <button type="submit" class="btn-primary">Rechercher</button>
            </form>
            <nav class="-mx-1 mt-4 flex gap-2 overflow-x-auto px-1 pb-1" aria-label="Filtrer par rôle">
                @php $tabs = ['' => ['Tous', $counts->sum()], 'client' => ['Clients', $counts['client'] ?? 0], 'expert' => ['Experts', $counts['expert'] ?? 0], 'manager' => ['Gestionnaires', $counts['manager'] ?? 0], 'admin' => ['Administrateurs', $counts['admin'] ?? 0]]; @endphp
                @foreach($tabs as $key => [$label, $count])
                    @php $active = (string) $currentRole === (string) $key; @endphp
                    <a href="{{ route('admin.users.index', array_filter(['role' => $key, 'q' => $search])) }}"
                       class="inline-flex shrink-0 items-center gap-2 rounded-full px-3.5 py-1.5 text-sm font-medium transition {{ $active ? 'bg-dark text-white' : 'bg-cream text-dark hover:bg-sand' }}">
                        {{ $label }}
                        <span class="rounded-full px-1.5 text-xs {{ $active ? 'bg-white/20' : 'bg-white text-muted' }}">{{ $count }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="overflow-hidden rounded-xl border border-line bg-white">
            <ul class="divide-y divide-line">
                @forelse($users as $account)
                    @php $isMe = $account->is(auth()->user()); @endphp
                    <li class="p-4 sm:p-5 {{ $account->isSuspended() ? 'bg-cream/50' : '' }}" x-data="{ edit: false }">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center">
                            <div class="flex min-w-0 flex-1 items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-cream text-sm font-semibold text-dark" aria-hidden="true">
                                    {{ collect(explode(' ', $account->name))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') }}
                                </span>
                                <div class="min-w-0">
                                    <p class="flex flex-wrap items-center gap-2 font-semibold text-dark">
                                        {{ $account->name }}
                                        @if($isMe)<span class="text-xs font-normal text-muted">(vous)</span>@endif
                                        @if($account->isSuspended())
                                            <span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-800 ring-1 ring-inset ring-red-200">Suspendu</span>
                                        @endif
                                    </p>
                                    <p class="truncate text-sm text-muted">{{ $account->email }}{{ $account->phone ? ' · '.$account->phone : '' }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted md:w-64">
                                <span class="font-medium text-dark">{{ $account->roleLabel() }}</span>
                                @if($account->isExpert())
                                    <span>{{ $account->open_projects_count }} projet(s) en cours</span>
                                @elseif($account->role === 'client')
                                    <span>{{ $account->service_requests_count }} demande(s)</span>
                                @endif
                                <span class="w-full text-xs">Inscrit le {{ $account->created_at->format('d/m/Y') }}</span>
                            </div>
                            <button type="button" @click="edit = !edit" class="btn-outline !py-2 md:shrink-0" x-text="edit ? 'Fermer' : 'Gérer'">Gérer</button>
                        </div>

                        <div x-show="edit" x-cloak x-transition class="mt-4 grid gap-4 rounded-xl bg-cream p-4 md:grid-cols-3">
                            <form method="POST" action="{{ route('admin.users.update', $account) }}" class="space-y-2">
                                @csrf
                                @method('PATCH')
                                <label for="role-{{ $account->id }}" class="field-label">Rôle</label>
                                <div class="flex gap-2">
                                    <select id="role-{{ $account->id }}" name="role" class="field-input" @disabled($isMe)>
                                        @foreach(\App\Models\User::ROLE_LABELS as $value => $label)
                                            <option value="{{ $value }}" @selected($account->role === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-primary !py-2" @disabled($isMe)>OK</button>
                                </div>
                            </form>
                            <div class="space-y-2">
                                <span class="field-label">Mot de passe</span>
                                <form method="POST" action="{{ route('admin.users.password-link', $account) }}">
                                    @csrf
                                    <button type="submit" class="btn-outline w-full !py-2">Envoyer un lien de réinitialisation</button>
                                </form>
                            </div>
                            <div class="space-y-2">
                                <span class="field-label">Accès</span>
                                @unless($isMe)
                                    <form method="POST" action="{{ route('admin.users.suspension', $account) }}"
                                          @unless($account->isSuspended()) onsubmit="return confirm('Suspendre le compte de {{ addslashes($account->name) }} ? La personne sera déconnectée.')" @endunless>
                                        @csrf
                                        @if($account->isSuspended())
                                            <button type="submit" class="btn-outline w-full !py-2">Réactiver le compte</button>
                                        @else
                                            <button type="submit" class="btn w-full border border-red-200 bg-white !py-2 text-red-700 hover:bg-red-50">Suspendre le compte</button>
                                        @endif
                                    </form>
                                @else
                                    <p class="text-sm text-muted">Vous ne pouvez pas suspendre votre propre compte.</p>
                                @endunless
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-14 text-center">
                        <p class="text-lg font-semibold text-dark">Aucun utilisateur trouvé</p>
                        <p class="mt-2 text-sm text-muted">Modifiez la recherche ou le filtre.</p>
                    </li>
                @endforelse
            </ul>
        </div>

        <div>{{ $users->links() }}</div>
    </div>
</section>
@endsection
