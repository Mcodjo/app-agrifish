@php
    $messages = [
        'service-request-created' => 'Votre demande a bien été envoyée. Notre équipe l’analyse et vous répond rapidement.',
        'request-cancelled' => 'Demande annulée. L’équipe a été prévenue.',
        'documents-added' => 'Documents ajoutés à la demande.',
        'document-removed' => 'Document retiré.',
        'milestone-moved' => 'Ordre des étapes mis à jour.',
        'milestone-deleted' => 'Étape supprimée.',
        'deliverable-removed' => 'Livrable retiré.',
        'user-created' => 'Compte créé. Un lien pour choisir son mot de passe a été envoyé par e-mail.',
        'user-updated' => 'Compte mis à jour.',
        'user-suspended' => 'Compte suspendu : la personne ne peut plus se connecter.',
        'user-reactivated' => 'Compte réactivé.',
        'password-link-sent' => 'Lien de réinitialisation du mot de passe envoyé.',
        'cart-added' => 'Produit ajouté au panier.',
        'cart-removed' => 'Article retiré du panier.',
        'coupon-applied' => 'Code promo appliqué.',
        'order-placed' => 'Commande enregistrée. Un récapitulatif vous a été envoyé par e-mail.',
        'payment-approved' => 'Paiement reçu : votre commande est confirmée.',
        'payment-pending' => 'Paiement en cours de vérification. Vous recevrez un e-mail dès sa confirmation.',
        'payment-failed' => 'Le paiement n’a pas abouti. Vous pouvez réessayer depuis cette page.',
        'payment-unavailable' => 'Le paiement en ligne est momentanément indisponible. Réessayez dans quelques minutes.',
        'reorder-done' => 'Les articles disponibles ont été ajoutés au panier.',
        'reorder-empty' => 'Aucun article de cette commande n’est disponible actuellement.',
        'address-saved' => 'Adresse enregistrée.',
        'address-deleted' => 'Adresse supprimée.',
        'review-sent' => 'Merci ! Votre avis sera publié après validation.',
        'order-updated' => 'Commande mise à jour. Le client a été prévenu.',
        'order-collected' => 'Paiement encaissé enregistré.',
        'order-cancelled' => 'Commande annulée, stock remis à jour. Le client a été prévenu.',
        'order-refunded' => 'Remboursement enregistré. Le client a été prévenu.',
        'note-added' => 'Note ajoutée à l’historique.',
        'product-created' => 'Produit créé.',
        'product-updated' => 'Produit enregistré.',
        'product-deleted' => 'Produit archivé : il n’apparaît plus dans la boutique.',
        'stock-updated' => 'Stock mis à jour.',
        'category-saved' => 'Catégorie enregistrée.',
        'category-deleted' => 'Catégorie supprimée.',
        'coupon-saved' => 'Code promo enregistré.',
        'coupon-deleted' => 'Code promo supprimé.',
        'review-approved' => 'Avis publié.',
        'review-rejected' => 'Avis refusé.',
        'review-deleted' => 'Avis supprimé.',
        'settings-saved' => 'Réglages enregistrés.',
        'two-factor-sent' => 'Un nouveau code vous a été envoyé par e-mail.',
        'message-sent' => 'Message envoyé.',
        'milestone-created' => 'Étape ajoutée.',
        'milestone-updated' => 'Étape mise à jour.',
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
        <div class="flex items-start gap-3 rounded-xl border border-line bg-white p-4 shadow-lg">
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
