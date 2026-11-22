<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private const FILTERS = [
        'en-cours' => 'En cours',
        'terminees' => 'Terminées',
        'annulees' => 'Annulées',
    ];

    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user->isAdmin() ? 'admin' : ($user->isExpert() ? 'expert' : 'client');

        // Admin : toutes les demandes. Expert : celles qui lui sont assignées. Client : les siennes.
        $scoped = fn (): Builder => ServiceRequest::query()
            ->when($role === 'expert', fn (Builder $query) => $query->where('assigned_expert_id', $user->id))
            ->when($role === 'client', fn (Builder $query) => $query->where('client_id', $user->id));

        $filter = array_key_exists((string) $request->query('filtre'), self::FILTERS) ? $request->query('filtre') : null;
        $search = trim((string) $request->query('q', ''));

        $requests = $scoped()
            ->with(['assignedExpert', 'client', 'milestones' => fn ($query) => $query->orderBy('sort_order')->orderBy('id')])
            ->withCount(['milestones', 'messages'])
            ->when($filter === 'en-cours', fn (Builder $query) => $query->whereNotIn('status', ServiceRequest::CLOSED_STATUSES))
            ->when($filter === 'terminees', fn (Builder $query) => $query->whereIn('status', ['completed', 'closed']))
            ->when($filter === 'annulees', fn (Builder $query) => $query->where('status', 'cancelled'))
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($search): void {
                $query->where('reference', 'like', "%{$search}%")->orWhere('title', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = $scoped()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $alerts = $this->alerts($scoped(), $user);

        $stats = [
            'total' => $counts->sum(),
            'open' => $counts->except(ServiceRequest::CLOSED_STATUSES)->sum(),
            'completed' => $counts->only(['completed', 'closed'])->sum(),
            'alerts' => $alerts->count(),
        ];

        return view('dashboard', [
            'requests' => $requests,
            'stats' => $stats,
            'alerts' => $alerts->take(5)->values(),
            'role' => $role,
            'filters' => self::FILTERS,
            'currentFilter' => $filter,
            'search' => $search,
            'indicators' => $role === 'admin' ? $this->indicators($counts) : null,
        ]);
    }

    /** Ce qui attend une intervention de l'utilisateur. */
    private function alerts(Builder $query, User $user)
    {
        return $query
            ->whereNotIn('status', ServiceRequest::CLOSED_STATUSES)
            ->with(['milestones' => fn ($query) => $query->orderBy('sort_order')])
            ->latest()
            ->limit(100)
            ->get()
            ->map(function (ServiceRequest $serviceRequest) use ($user) {
                $blocked = $serviceRequest->milestones->firstWhere('status', 'blocked');

                $message = match (true) {
                    $serviceRequest->status === 'waiting_client' => $user->isExpert() || $user->isAdmin()
                        ? 'En attente d’une réponse du client.'
                        : 'Votre réponse est attendue pour avancer.',
                    $blocked !== null => 'Étape bloquée : '.$blocked->title.'.',
                    $user->isAdmin() && $serviceRequest->status === 'new' => 'Nouvelle demande à analyser et assigner.',
                    $user->isAdmin() && ! $serviceRequest->assigned_expert_id => 'Aucun expert assigné.',
                    $user->isExpert() && $serviceRequest->milestones->isEmpty() => 'Aucune étape définie : planifiez le projet.',
                    default => null,
                };

                return $message ? [
                    'reference' => $serviceRequest->reference,
                    'title' => $serviceRequest->title,
                    'message' => $message,
                    'tone' => $blocked && $serviceRequest->status !== 'waiting_client' ? 'red' : 'amber',
                    'url' => route('projects.show', $serviceRequest),
                ] : null;
            })
            ->filter()
            ->values();
    }

    private function indicators($counts): array
    {
        $since = now()->subDays(90);

        // Délai entre le dépôt d'une demande et sa première prise en charge (statut ou assignation).
        $firstHandling = ServiceRequestEvent::query()
            ->whereIn('type', ['status', 'assigned'])
            ->whereHas('serviceRequest', fn (Builder $query) => $query->where('created_at', '>=', $since))
            ->selectRaw('service_request_id, min(created_at) as handled_at')
            ->groupBy('service_request_id')
            ->with('serviceRequest:id,created_at')
            ->get()
            ->map(fn ($row) => $row->serviceRequest ? max(0, $row->serviceRequest->created_at->diffInHours($row->handled_at)) : null)
            ->filter(fn ($hours) => $hours !== null);

        return [
            'by_status' => collect(ServiceRequest::STATUSES)->map(fn ($label, $key) => [
                'label' => $label,
                'total' => (int) ($counts[$key] ?? 0),
            ])->filter(fn ($row) => $row['total'] > 0)->all(),
            'this_month' => ServiceRequest::query()->where('created_at', '>=', now()->startOfMonth())->count(),
            'unassigned' => ServiceRequest::query()->whereNull('assigned_expert_id')->whereNotIn('status', ServiceRequest::CLOSED_STATUSES)->count(),
            'average_handling_hours' => $firstHandling->isEmpty() ? null : (int) round($firstHandling->avg()),
            'clients' => User::query()->where('role', 'client')->count(),
            'experts' => User::query()
                ->where('role', 'expert')
                ->whereNull('suspended_at')
                ->withCount(['assignedRequests as open_projects_count' => fn ($query) => $query->whereNotIn('status', ServiceRequest::CLOSED_STATUSES)])
                ->orderByDesc('open_projects_count')
                ->orderBy('name')
                ->get(),
        ];
    }
}
