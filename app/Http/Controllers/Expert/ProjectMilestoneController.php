<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;
use App\Models\ProjectMilestone;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectMilestoneController extends Controller
{
    public function store(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->authorizeExpert($request, $serviceRequest);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:3000'],
            'status' => ['required', 'in:planned,in_progress,validated,blocked,completed'],
            'due_date' => ['nullable', 'date'],
        ]);

        $serviceRequest->milestones()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'due_date' => $validated['due_date'] ?? null,
            'sort_order' => (int) $serviceRequest->milestones()->max('sort_order') + 1,
        ]);

        return back()->with('status', 'milestone-created');
    }

    public function update(Request $request, ProjectMilestone $projectMilestone): RedirectResponse
    {
        $this->authorizeExpert($request, $projectMilestone->serviceRequest);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:3000'],
            'status' => ['required', 'in:planned,in_progress,validated,blocked,completed'],
            'due_date' => ['nullable', 'date'],
            'deliverables' => ['nullable', 'array'],
            'deliverables.*' => ['file', 'max:5120', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg'],
        ]);

        $deliverablePaths = $projectMilestone->deliverable_paths ?? [];

        foreach ($request->file('deliverables', []) as $deliverable) {
            $deliverablePaths[] = $deliverable->store('project-deliverables/'.$projectMilestone->id, 'public');
        }

        $projectMilestone->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'due_date' => $validated['due_date'] ?? null,
            'deliverable_paths' => $deliverablePaths,
        ]);

        return back()->with('status', 'milestone-updated');
    }

    protected function authorizeExpert(Request $request, ServiceRequest $serviceRequest): void
    {
        $user = $request->user();

        abort_unless($user && ($user->isAdmin() || $user->isExpert()), 403);
    }
}
