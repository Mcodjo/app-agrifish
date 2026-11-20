<section>
    <header>
        <h2 class="text-[17px] font-semibold text-dark">Supprimer le compte</h2>
        <p class="mt-1 max-w-xl text-sm leading-6 text-muted">La suppression est définitive : vos demandes, documents et messages seront effacés. Téléchargez au préalable les fichiers que vous souhaitez conserver.</p>
    </header>

    <x-danger-button class="mt-5" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">Supprimer mon compte</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-dark">Supprimer définitivement votre compte ?</h2>
            <p class="mt-2 text-sm leading-6 text-muted">Saisissez votre mot de passe pour confirmer. Cette action ne peut pas être annulée.</p>

            <div class="mt-6">
                <x-input-label for="password" value="Mot de passe" class="sr-only" />
                <x-text-input id="password" name="password" type="password" placeholder="Mot de passe" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close')">Annuler</x-secondary-button>
                <x-danger-button>Supprimer le compte</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
