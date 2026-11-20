<x-guest-layout title="Confirmation">
    <h1 class="font-serif text-4xl tracking-[-0.01em] text-dark">Confirmez votre mot de passe</h1>
    <p class="mt-3 text-[15px] leading-7 text-muted">Cette action est protégée. Saisissez votre mot de passe pour continuer.</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">Confirmer</x-primary-button>
    </form>
</x-guest-layout>
