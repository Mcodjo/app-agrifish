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

    /**
     * Envoyer un message sur un projet.
     */
    public function message(User $user, ServiceRequest $serviceRequest): bool
    {
        return $this->view($user, $serviceRequest);
    }

    /**
     * Créer ou modifier des jalons et déposer des livrables.
     */
    public function manageMilestones(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isExpert() && $serviceRequest->assigned_expert_id === $user->id;
    }
}
