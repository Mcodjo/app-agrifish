<x-guest-layout title="Vérification de l’adresse e-mail">
    <h1 class="font-serif text-4xl tracking-[-0.01em] text-dark">Vérifiez votre adresse e-mail</h1>
    <p class="mt-3 text-[15px] leading-7 text-muted">Nous vous avons envoyé un lien de validation. Cliquez dessus pour activer votre compte.</p>

    @if (session('status') == 'verification-link-sent')
        <x-auth-session-status class="mt-6" status="Un nouveau lien a été envoyé à l’adresse utilisée lors de l’inscription." />
    @endif

    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>Renvoyer l’e-mail</x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-muted underline-offset-2 hover:text-dark hover:underline">Se déconnecter</button>
        </form>
    </div>
</x-guest-layout>
