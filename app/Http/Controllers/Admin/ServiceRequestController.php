<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Notifications\ProjectAssigned;
use App\Notifications\ServiceRequestStatusChanged;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('statut');
        $status = array_key_exists((string) $status, ServiceRequest::STATUSES) ? $status : null;
        $search = trim((string) $request->query('q', ''));

        $requests = ServiceRequest::query()
            ->with(['client', 'assignedExpert'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('reference', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhereHas('client', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = ServiceRequest::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $experts = User::query()
            ->where('role', 'expert')
            ->whereNull('suspended_at')
            ->withCount(['assignedRequests as open_projects_count' => fn ($query) => $query->whereNotIn('status', ServiceRequest::CLOSED_STATUSES)])
            ->orderBy('name')
            ->get();

        return view('admin.requests.index', [
            'requests' => $requests,
            'experts' => $experts,
            'statuses' => ServiceRequest::STATUSES,
            'counts' => $counts,
            'currentStatus' => $status,
            'search' => $search,
        ]);
    }

    public function update(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(ServiceRequest::STATUSES))],
            // Uniquement des utilisateurs ayant le rôle expert.
            'assigned_expert_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 'expert')->whereNull('suspended_at')],
            'status_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $previousStatus = $serviceRequest->status;
        $previousExpert = $serviceRequest->assigned_expert_id;
        $newExpert = isset($validated['assigned_expert_id']) ? (int) $validated['assigned_expert_id'] : null;
        $statusChanged = $previousStatus !== $validated['status'];
        $expertChanged = $previousExpert !== $newExpert;

        $serviceRequest->fill([
            'status' => $validated['status'],
            'assigned_expert_id' => $newExpert,
            'status_notes' => $validated['status_notes'] ?? null,
        ]);

        if ($statusChanged) {
            $serviceRequest->status_changed_at = now();
        }

        $serviceRequest->save();
        $serviceRequest->load(['client', 'assignedExpert']);

        if ($statusChanged) {
            $serviceRequest->record('status', $request->user(), [
                'from_status' => $previousStatus,
                'to_status' => $serviceRequest->status,
            ]);
        }

        if ($expertChanged) {
            $serviceRequest->record($newExpert ? 'assigned' : 'unassigned', $request->user(), [
                'details' => $serviceRequest->assignedExpert?->name,
            ]);

            $expert = $serviceRequest->assignedExpert;

            if ($expert && $expert->id !== $request->user()->id && ! $expert->isSuspended()) {
                $expert->notify(new ProjectAssigned($serviceRequest));
            }
        }

        $client = $serviceRequest->client;

        // Notifier seulement si le statut change et si le client accepte ces e-mails.
        if ($statusChanged && $client && $client->wantsEmail('status_notifications')) {
            $client->notify(new ServiceRequestStatusChanged($serviceRequest));
        }

        return back()->with('status', 'request-updated');
    }
}
