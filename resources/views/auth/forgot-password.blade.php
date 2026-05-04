<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">Mot de passe oublié</h1>
        <p class="mt-2 text-sm text-gray-600">Saisissez votre email et nous vous enverrons un lien de réinitialisation.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Adresse email')" />
            <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus placeholder="vous@exemple.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                Recevoir le lien
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
