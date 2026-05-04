<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $requests = ServiceRequest::query()
            ->with(['assignedExpert', 'milestones' => function ($query): void {
                $query->orderBy('sort_order')->orderBy('id');
            }])
            ->withCount(['milestones', 'messages'])
            ->where('client_id', $user->id)
            ->latest()
            ->get();

        $stats = [
            'total' => $requests->count(),
            'open' => $requests->whereNotIn('status', ['completed', 'closed'])->count(),
            'completed' => $requests->where('status', 'completed')->count(),
            'alerts' => $requests->whereIn('status', ['new', 'reviewing', 'waiting_client'])->count(),
        ];

        $alerts = $requests->take(3)->map(function (ServiceRequest $serviceRequest) {
            return [
                'title' => $serviceRequest->reference,
                'message' => match ($serviceRequest->status) {
                    'new' => 'Nouvelle demande reçue.',
                    'reviewing' => 'Demande en cours d’analyse.',
                    'assigned' => 'Un expert a été assigné.',
                    'in_progress' => 'Le traitement a démarré.',
                    'waiting_client' => 'Une réponse du client est attendue.',
                    'completed' => 'Demande finalisée.',
                    'closed' => 'Demande clôturée.',
                    default => 'Mise à jour récente.',
                },
            ];
        });

        return view('dashboard', compact('requests', 'stats', 'alerts'));
    }
}
