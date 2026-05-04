<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Notifications\ServiceRequestStatusChanged;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $requests = ServiceRequest::query()
            ->with(['client', 'assignedExpert'])
            ->latest()
            ->get();

        $experts = User::query()
            ->where('role', 'expert')
            ->orderBy('name')
            ->get();

        return view('admin.requests.index', compact('requests', 'experts'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,reviewing,assigned,in_progress,waiting_client,completed,closed'],
            'assigned_expert_id' => ['nullable', 'exists:users,id'],
            'status_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $serviceRequest->fill([
            'status' => $validated['status'],
            'assigned_expert_id' => $validated['assigned_expert_id'] ?? null,
            'status_notes' => $validated['status_notes'] ?? null,
            'status_changed_at' => now(),
        ])->save();

        $serviceRequest->client->notify(new ServiceRequestStatusChanged($serviceRequest));

        return back()->with('status', 'request-updated');
    }
}
