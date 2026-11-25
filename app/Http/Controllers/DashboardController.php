<?php

namespace App\Http\Controllers;

use App\Models\ProjectMilestone;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestEvent;
use App\Models\Shop\Order;
use App\Models\Shop\ProductReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public const FILTERS = [
        'en-cours' => 'En cours',
        'terminees' => 'Terminées',
        'annulees' => 'Annulées',
    ];

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isManager()) {
            return redirect()->route('shop-admin.dashboard');
        }

        $role = $user->isAdmin() ? 'admin' : ($user->isExpert() ? 'expert' : 'client');
        $counts = self::scoped($user)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $alerts = self::alerts(self::scoped($user), $user);

        $openProjects = self::scoped($user)
            ->with(['assignedExpert', 'client', 'milestones' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
            ->withCount(['milestones', 'messages'])
            ->whereNotIn('status', ServiceRequest::CLOSED_STATUSES)
            ->latest('updated_at')
            ->take($role === 'admin' ? 5 : 4)
            ->get();

        $data = [
            'role' => $role,
            'stats' => [
                'total' => $counts->sum(),
                'open' => $counts->except(ServiceRequest::CLOSED_STATUSES)->sum(),
                'completed' => $counts->only(['completed', 'closed'])->sum(),
                'alerts' => $alerts->count(),
            ],
            'alerts' => $alerts->take(6)->values(),
            'openProjects' => $openProjects,
        ];

        if ($role === 'client') {
            $data['orders'] = $user->orders()->withCount('items')->take(3)->get();
        }

        if ($role === 'expert') {
            $data['deadlines'] = ProjectMilestone::query()
                ->with('serviceRequest')
                ->whereHas('serviceRequest', fn ($q) => $q->where('assigned_expert_id', $user->id)->whereNotIn('status', ServiceRequest::CLOSED_STATUSES))
                ->whereNotIn('status', ['validated', 'completed'])
                ->whereNotNull('due_date')
                ->where('due_date', '<=', now()->addDays(14))
                ->orderBy('due_date')
                ->take(6)
                ->get();
        }

        if ($role === 'admin') {
            $data['indicators'] = self::indicators($counts);
            $data['shop'] = [
                'month' => (int) Order::query()->whereIn('payment_status', ['paid', 'partially_refunded', 'refunded'])
                    ->where('paid_at', '>=', now()->startOfMonth())->sum(DB::raw('total - refunded_amount')),
                'to_process' => Order::query()->whereIn('status', ['new', 'paid', 'preparing', 'ready'])->count(),
                'reviews' => ProductReview::query()->where('status', 'pending')->count(),
            ];
        }

        return view('dashboard', $data);
    }

    /** Demandes visibles par l'utilisateur : toutes (admin), assignées (expert) ou les siennes (client). */
    public static function scoped(User $user): Builder
    {
        return ServiceRequest::query()
            ->when($user->isExpert(), fn (Builder $q) => $q->where('assigned_expert_id', $user->id))
            ->when(! $user->isExpert() && ! $user->isAdmin(), fn (Builder $q) => $q->where('client_id', $user->id));
    }

    /** Ce qui attend une intervention de l'utilisateur. */
    public static function alerts(Builder $query, User $user): Collection
    {
        return $query
            ->whereNotIn('status', ServiceRequest::CLOSED_STATUSES)
            ->with(['milestones' => fn ($q) => $q->orderBy('sort_order')])
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

    private static function indicators($counts): array
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
