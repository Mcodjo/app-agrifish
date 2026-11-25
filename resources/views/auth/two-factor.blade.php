<x-guest-layout title="Vérification de connexion">
    <h1 class="font-serif text-[2rem] leading-tight text-dark">Vérification</h1>
    <p class="mt-2 text-[15px] leading-7 text-muted">
        Pour protéger l’administration, nous avons envoyé un code à 6 chiffres à <strong class="text-dark">{{ $email }}</strong>. Il est valable 10 minutes.
    </p>

    @if(session('status') === 'two-factor-sent')
        <p class="mt-4 rounded-lg bg-primary-50 px-3 py-2 text-sm text-primary-800">Un nouveau code vous a été envoyé.</p>
    @endif

    <form method="POST" action="{{ route('two-factor.verify') }}" class="mt-8 space-y-5">
        @csrf
        <div>
            <label for="code" class="field-label">Code reçu par e-mail</label>
            <input id="code" name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required autofocus
                   class="field-input text-center text-2xl tracking-[0.5em]">
            @error('code')<p class="field-error">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-primary w-full">Valider</button>
    </form>

    <form method="POST" action="{{ route('two-factor.resend') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-sm text-muted underline-offset-2 hover:text-dark hover:underline">Recevoir un nouveau code</button>
    </form>
    <p class="mt-6 text-center text-sm"><a href="{{ route('login') }}" class="text-muted hover:text-dark">Revenir à la connexion</a></p>
</x-guest-layout>
