<x-guest-layout title="Créer un compte">
    <h1 class="font-serif text-4xl tracking-[-0.01em] text-dark">Créer un compte</h1>
    <p class="mt-3 text-[15px] leading-7 text-muted">Votre espace vous permet de déposer une demande, de partager vos documents et de suivre votre projet.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <x-input-label for="name" value="Nom complet" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <p class="mt-1.5 text-xs text-muted">8 caractères minimum.</p>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">Créer mon compte</x-primary-button>

        <p class="text-xs leading-5 text-muted">En créant un compte, vous acceptez le traitement de vos données décrit dans la <a href="{{ route('politique-confidentialite') }}" class="underline underline-offset-2">politique de confidentialité</a>.</p>
    </form>

    <p class="mt-8 border-t border-line pt-6 text-sm text-muted">
        Déjà inscrit ?
        <a href="{{ route('login') }}" class="font-semibold text-dark underline-offset-2 hover:underline">Se connecter</a>
    </p>
</x-guest-layout>
