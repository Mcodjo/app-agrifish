<?php

namespace App\Policies;

use App\Models\ServiceRequest;
use App\Models\User;

class ServiceRequestPolicy
{
    /**
     * Consulter un projet, ses documents, ses livrables et sa messagerie.
     * Admin : tout. Expert : uniquement les projets qui lui sont assignés. Client : uniquement les siens.
     */
    public function view(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isExpert()) {
            return $serviceRequest->assigned_expert_id === $user->id;
        }

        return $serviceRequest->client_id === $user->id;
    }

    public function message(User $user, ServiceRequest $serviceRequest): bool
    {
        return $this->view($user, $serviceRequest) && $serviceRequest->status !== 'cancelled';
    }

    /**
     * Créer, modifier, réordonner ou supprimer des étapes et leurs livrables.
     */
    public function manageMilestones(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($serviceRequest->status === 'cancelled') {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isExpert() && $serviceRequest->assigned_expert_id === $user->id;
    }

    /**
     * Le client annule sa demande tant que le travail n'a pas commencé.
     */
    public function cancel(User $user, ServiceRequest $serviceRequest): bool
    {
        return $serviceRequest->client_id === $user->id && $serviceRequest->isEditableByClient();
    }

    public function addDocuments(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($serviceRequest->isClosed()) {
            return false;
        }

        return $user->isAdmin() || $serviceRequest->client_id === $user->id;
    }

    public function removeDocuments(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $serviceRequest->client_id === $user->id && $serviceRequest->isEditableByClient();
    }
}
