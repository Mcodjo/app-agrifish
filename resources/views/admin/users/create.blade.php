@extends('layouts.app')

@section('title', 'Nouveau compte')
@section('espace', true)

@section('content')
<x-app-header :back="route('admin.users.index')" back-label="Utilisateurs" eyebrow="Administration"
              title="Nouveau compte"
              subtitle="Créez le compte d’un expert, d’un administrateur ou d’un client. La personne reçoit un e-mail pour choisir son mot de passe." />

<section class="max-w-content px-site py-10 lg:py-12">
    <form method="POST" action="{{ route('admin.users.store') }}" class="card max-w-2xl space-y-5">
        @csrf
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="name" class="field-label">Nom complet *</label>
                <input id="name" name="name" type="text" required maxlength="255" value="{{ old('name') }}" class="field-input" autocomplete="off">
                @error('name')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="field-label">E-mail *</label>
                <input id="email" name="email" type="email" required maxlength="255" value="{{ old('email') }}" class="field-input" autocomplete="off">
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" class="field-label">Téléphone</label>
                <input id="phone" name="phone" type="tel" maxlength="30" value="{{ old('phone') }}" class="field-input">
                @error('phone')<p class="field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <fieldset>
            <legend class="field-label">Rôle *</legend>
            <div class="mt-1 grid gap-3 sm:grid-cols-3">
                @foreach([
                    'expert' => 'Suit les projets qui lui sont assignés.',
                    'admin' => 'Gère les demandes, les comptes et les assignations.',
                    'client' => 'Dépose des demandes et suit ses projets.',
                ] as $value => $help)
                    <label class="flex cursor-pointer gap-3 rounded-xl border border-line p-4 text-sm has-[:checked]:border-primary-800 has-[:checked]:bg-primary-50/40">
                        <input type="radio" name="role" value="{{ $value }}" class="mt-0.5 h-4 w-4 border-line text-primary-800 focus:ring-primary-500" @checked(old('role', 'expert') === $value)>
                        <span>
                            <span class="block font-semibold text-dark">{{ \App\Models\User::ROLE_LABELS[$value] }}</span>
                            <span class="mt-0.5 block text-muted">{{ $help }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            @error('role')<p class="field-error">{{ $message }}</p>@enderror
        </fieldset>

        <div class="flex justify-end gap-2 border-t border-line pt-5">
            <a href="{{ route('admin.users.index') }}" class="btn-outline">Annuler</a>
            <button type="submit" class="btn-primary">Créer le compte</button>
        </div>
    </form>
</section>
@endsection
