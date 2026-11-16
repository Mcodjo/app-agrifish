<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = ServiceRequest::query()
            ->with(['assignedExpert', 'client', 'milestones' => function ($query): void {
                $query->orderBy('sort_order')->orderBy('id');
            }])
            ->withCount(['milestones', 'messages'])
            ->latest();

        // Admin : toutes les demandes. Expert : celles qui lui sont assignées. Client : les siennes.
        if ($user->isExpert()) {
            $query->where('assigned_expert_id', $user->id);
        } elseif (! $user->isAdmin()) {
            $query->where('client_id', $user->id);
        }

        $requests = $query->get();

        $stats = [
            'total' => $requests->count(),
            'open' => $requests->whereNotIn('status', ['completed', 'closed'])->count(),
            'completed' => $requests->where('status', 'completed')->count(),
            'alerts' => $requests->whereIn('status', ['new', 'reviewing', 'waiting_client'])->count(),
        ];

        // « Actions requises » : uniquement ce qui demande vraiment une intervention de l'utilisateur.
        $alerts = $requests->map(function (ServiceRequest $serviceRequest) use ($user) {
            $blocked = $serviceRequest->milestones->firstWhere('status', 'blocked');

            $message = match (true) {
                $serviceRequest->status === 'waiting_client' => $user->isExpert() || $user->isAdmin()
                    ? 'En attente d’une réponse du client.'
                    : 'Votre réponse est attendue pour avancer.',
                $blocked !== null => 'Jalon bloqué : '.$blocked->title.'.',
                $user->isAdmin() && $serviceRequest->status === 'new' => 'Nouvelle demande à analyser et assigner.',
                $user->isExpert() && $serviceRequest->milestones->isEmpty() => 'Aucun jalon défini : planifiez les étapes.',
                default => null,
            };

            return $message ? [
                'reference' => $serviceRequest->reference,
                'title' => $serviceRequest->title,
                'message' => $message,
                'tone' => $blocked && $serviceRequest->status !== 'waiting_client' ? 'red' : 'amber',
                'url' => route('projects.show', $serviceRequest),
            ] : null;
        })->filter()->take(5)->values();

        $stats['alerts'] = $alerts->count();

        $role = $user->isAdmin() ? 'admin' : ($user->isExpert() ? 'expert' : 'client');

        return view('dashboard', compact('requests', 'stats', 'alerts', 'role'));
    }
}
