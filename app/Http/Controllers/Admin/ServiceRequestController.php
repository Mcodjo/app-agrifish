<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Notifications\ServiceRequestStatusChanged;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $requests = ServiceRequest::query()
            ->with(['client', 'assignedExpert'])
            ->latest()
            ->paginate(20);

        $experts = User::query()
            ->where('role', 'expert')
            ->orderBy('name')
            ->get();

        return view('admin.requests.index', [
            'requests' => $requests,
            'experts' => $experts,
            'statuses' => ServiceRequest::STATUSES,
        ]);
    }

    public function update(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(ServiceRequest::STATUSES))],
            // Seul un utilisateur ayant le rôle « expert » peut être assigné.
            'assigned_expert_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 'expert')],
            'status_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $statusChanged = $serviceRequest->status !== $validated['status'];

        $serviceRequest->fill([
            'status' => $validated['status'],
            'assigned_expert_id' => $validated['assigned_expert_id'] ?? null,
            'status_notes' => $validated['status_notes'] ?? null,
        ]);

        if ($statusChanged) {
            $serviceRequest->status_changed_at = now();
        }

        $serviceRequest->save();

        $client = $serviceRequest->client;

        // On ne notifie que lors d'un vrai changement de statut, et seulement si le client ne l'a pas désactivé.
        if ($statusChanged && $client && ($client->preferences['status_notifications'] ?? true)) {
            $client->notify(new ServiceRequestStatusChanged($serviceRequest));
        }

        return back()->with('status', 'request-updated');
    }
}
