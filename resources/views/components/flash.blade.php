@php
    $messages = [
        'service-request-created' => 'Votre demande a bien été envoyée. Notre équipe l’analyse et vous répond rapidement.',
        'message-sent' => 'Message envoyé.',
        'milestone-created' => 'Jalon créé.',
        'milestone-updated' => 'Jalon mis à jour.',
        'request-updated' => 'Demande mise à jour. Le client a été notifié si le statut a changé.',
        'profile-updated' => 'Profil enregistré.',
        'password-updated' => 'Mot de passe modifié.',
    ];
    $flash = $messages[session('status')] ?? null;
@endphp

@if($flash)
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show" x-transition.opacity.duration.300ms
         role="status" aria-live="polite"
         class="fixed inset-x-4 top-20 z-[60] mx-auto max-w-md sm:inset-x-auto sm:right-6 sm:top-24">
        <div class="flex items-start gap-3 rounded-2xl bg-white p-4 shadow-2xl ring-1 ring-primary-100">
            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary-50 text-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </span>
            <p class="flex-1 text-sm font-medium text-dark">{{ $flash }}</p>
            <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600" aria-label="Fermer">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
@endif
