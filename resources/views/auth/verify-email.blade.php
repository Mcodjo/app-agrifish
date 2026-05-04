<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">Vérifiez votre email</h1>
        <p class="mt-2 text-sm text-gray-600">Un lien de validation vous a été envoyé. Cliquez dessus pour activer votre compte.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
            Un nouveau lien de vérification a été envoyé à l'adresse email utilisée lors de l'inscription.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Renvoyer l'email
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm font-semibold text-primary hover:text-primary-dark">
                Déconnexion
            </button>
        </form>
    </div>
</x-guest-layout>
