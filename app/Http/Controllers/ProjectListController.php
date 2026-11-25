<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectListController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        abort_if($user->isManager(), 403);

        $filter = array_key_exists((string) $request->query('filtre'), DashboardController::FILTERS) ? $request->query('filtre') : null;
        $search = trim((string) $request->query('q', ''));

        $projects = DashboardController::scoped($user)
            ->with(['assignedExpert', 'client', 'milestones' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
            ->withCount(['milestones', 'messages'])
            ->when($filter === 'en-cours', fn (Builder $q) => $q->whereNotIn('status', ServiceRequest::CLOSED_STATUSES))
            ->when($filter === 'terminees', fn (Builder $q) => $q->whereIn('status', ['completed', 'closed']))
            ->when($filter === 'annulees', fn (Builder $q) => $q->where('status', 'cancelled'))
            ->when($search !== '', fn (Builder $q) => $q->where(fn (Builder $q) => $q->where('reference', 'like', "%{$search}%")->orWhere('title', 'like', "%{$search}%")))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('projects.index', [
            'projects' => $projects,
            'filters' => DashboardController::FILTERS,
            'currentFilter' => $filter,
            'search' => $search,
            'role' => $user->isAdmin() ? 'admin' : ($user->isExpert() ? 'expert' : 'client'),
            'total' => DashboardController::scoped($user)->count(),
        ]);
    }
}
