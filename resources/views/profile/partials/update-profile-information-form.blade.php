<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Informations du profil</h2>

        <p class="mt-1 text-sm text-gray-600">
            Mettez à jour vos coordonnées et vos préférences de notification.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nom complet')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone" :value="__('Téléphone')" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <label class="flex items-center gap-3 rounded-2xl bg-cream px-4 py-3 text-sm font-medium text-gray-700">
                <input type="hidden" name="preferences[service_updates]" value="0">
                <input type="checkbox" name="preferences[service_updates]" value="1" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary" {{ old('preferences.service_updates', data_get($user->preferences, 'service_updates')) ? 'checked' : '' }}>
                Recevoir les mises à jour de service
            </label>
            <label class="flex items-center gap-3 rounded-2xl bg-cream px-4 py-3 text-sm font-medium text-gray-700">
                <input type="hidden" name="preferences[status_notifications]" value="0">
                <input type="checkbox" name="preferences[status_notifications]" value="1" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary" {{ old('preferences.status_notifications', data_get($user->preferences, 'status_notifications')) ? 'checked' : '' }}>
                Notifications de statut
            </label>
            <label class="flex items-center gap-3 rounded-2xl bg-cream px-4 py-3 text-sm font-medium text-gray-700">
                <input type="hidden" name="preferences[newsletter]" value="0">
                <input type="checkbox" name="preferences[newsletter]" value="1" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary" {{ old('preferences.newsletter', data_get($user->preferences, 'newsletter')) ? 'checked' : '' }}>
                Recevoir la newsletter
            </label>
            <div>
                <x-input-label for="preferred_contact" :value="__('Contact préféré')" />
                <select id="preferred_contact" name="preferences[preferred_contact]" class="mt-1 block w-full rounded-xl border-gray-200 text-sm focus:border-primary focus:ring-primary">
                    <option value="email" @selected(old('preferences.preferred_contact', data_get($user->preferences, 'preferred_contact', 'email')) === 'email')>Email</option>
                    <option value="phone" @selected(old('preferences.preferred_contact', data_get($user->preferences, 'preferred_contact')) === 'phone')>Téléphone</option>
                    <option value="whatsapp" @selected(old('preferences.preferred_contact', data_get($user->preferences, 'preferred_contact')) === 'whatsapp')>WhatsApp</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Enregistrer</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >Enregistré.</p>
            @endif
        </div>
    </form>
</section>
