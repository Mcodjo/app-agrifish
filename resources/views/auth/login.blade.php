<x-guest-layout title="Connexion">
    <h1 class="font-serif text-4xl tracking-[-0.01em] text-dark">Connexion</h1>
    <p class="mt-3 text-[15px] leading-7 text-muted">Accédez à votre espace pour suivre vos demandes, vos documents et vos échanges avec nos experts.</p>

    <x-auth-session-status class="mt-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-baseline justify-between">
                <x-input-label for="password" value="Mot de passe" />
                @if (Route::has('password.request'))
                    <a class="text-sm text-muted underline-offset-2 hover:text-dark hover:underline" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                @endif
            </div>
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="flex items-center gap-2.5 text-sm text-muted">
            <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-line text-primary-800 focus:ring-primary-500">
            Rester connecté
        </label>

        <x-primary-button class="w-full">Se connecter</x-primary-button>
    </form>

    <p class="mt-8 border-t border-line pt-6 text-sm text-muted">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="font-semibold text-dark underline-offset-2 hover:underline">Créer un compte</a>
    </p>
</x-guest-layout>
