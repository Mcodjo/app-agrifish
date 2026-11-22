<section>
    <header>
        <h2 class="text-[17px] font-semibold text-dark">Informations du profil</h2>

        <p class="mt-1 text-sm leading-6 text-muted">Vos coordonnées et vos préférences de notification.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nom complet" />
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone" value="Téléphone" />
            <x-text-input id="phone" name="phone" type="tel" :value="old('phone', $user->phone)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 text-sm text-muted">
                        Votre adresse e-mail n’est pas encore vérifiée.

                        <button form="send-verification" class="text-sm text-dark underline underline-offset-2">
                            Renvoyer l’e-mail de vérification.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-primary-800">
                            Un nouveau lien de vérification vous a été envoyé.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <fieldset>
            <legend class="field-label">E-mails de suivi</legend>
            @php
                $emailPrefs = ['message_notifications' => 'À chaque nouveau message sur un projet'];
                if (! $user->isExpert()) {
                    $emailPrefs['status_notifications'] = 'À chaque changement de statut de mes demandes';
                }
                if ($user->isAdmin()) {
                    $emailPrefs['request_notifications'] = 'À chaque nouvelle demande déposée';
                }
            @endphp
            <div class="mt-1 grid gap-3 sm:grid-cols-2">
                @foreach($emailPrefs as $key => $label)
                    <label class="flex items-center gap-3 rounded-[10px] border border-line px-4 py-3 text-sm text-dark">
                        <input type="hidden" name="preferences[{{ $key }}]" value="0">
                        <input type="checkbox" name="preferences[{{ $key }}]" value="1" class="h-4 w-4 rounded border-line text-primary-800 focus:ring-primary-500" @checked(old('preferences.'.$key, $user->wantsEmail($key)))>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </fieldset>

        <div class="grid gap-4 sm:grid-cols-2">
            <label class="flex items-center gap-3 rounded-[10px] border border-line px-4 py-3 text-sm text-dark">
                <input type="hidden" name="preferences[newsletter]" value="0">
                <input type="checkbox" name="preferences[newsletter]" value="1" class="h-4 w-4 rounded border-line text-primary-800 focus:ring-primary-500" {{ old('preferences.newsletter', data_get($user->preferences, 'newsletter')) ? 'checked' : '' }}>
                Recevoir la newsletter
            </label>
            <div>
                <x-input-label for="preferred_contact" value="Moyen de contact préféré" />
                <select id="preferred_contact" name="preferences[preferred_contact]" class="field-input">
                    <option value="email" @selected(old('preferences.preferred_contact', data_get($user->preferences, 'preferred_contact', 'email')) === 'email')>E-mail</option>
                    <option value="phone" @selected(old('preferences.preferred_contact', data_get($user->preferences, 'preferred_contact')) === 'phone')>Téléphone</option>
                    <option value="whatsapp" @selected(old('preferences.preferred_contact', data_get($user->preferences, 'preferred_contact')) === 'whatsapp')>WhatsApp</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Enregistrer</x-primary-button>
        </div>
    </form>
</section>
