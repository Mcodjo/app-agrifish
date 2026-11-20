<x-guest-layout title="Mot de passe oublié">
    <h1 class="font-serif text-4xl tracking-[-0.01em] text-dark">Mot de passe oublié</h1>
    <p class="mt-3 text-[15px] leading-7 text-muted">Indiquez l’adresse e-mail de votre compte. Nous vous enverrons un lien pour choisir un nouveau mot de passe.</p>

    <x-auth-session-status class="mt-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">Recevoir le lien</x-primary-button>
    </form>

    <p class="mt-8 border-t border-line pt-6 text-sm text-muted">
        <a href="{{ route('login') }}" class="font-semibold text-dark underline-offset-2 hover:underline">Retour à la connexion</a>
    </p>
</x-guest-layout>
