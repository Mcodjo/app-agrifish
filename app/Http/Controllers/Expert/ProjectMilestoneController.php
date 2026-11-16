<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;
use App\Models\ProjectMilestone;
use App\Models\ServiceRequest;
use App\Support\PrivateFiles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectMilestoneController extends Controller
{
    private const STATUSES = 'in:planned,in_progress,validated,blocked,completed';

    public function store(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        Gate::authorize('manageMilestones', $serviceRequest);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:3000'],
            'status' => ['required', self::STATUSES],
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
        Gate::authorize('manageMilestones', $projectMilestone->serviceRequest);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:3000'],
            'status' => ['required', self::STATUSES],
            'due_date' => ['nullable', 'date'],
            'deliverables' => ['nullable', 'array', 'max:10'],
            'deliverables.*' => ['file', 'max:5120', 'mimes:'.PrivateFiles::ALLOWED_MIMES],
        ]);

        $deliverablePaths = $projectMilestone->deliverable_paths ?? [];

        foreach ($request->file('deliverables', []) as $deliverable) {
            $deliverablePaths[] = PrivateFiles::store($deliverable, 'project-deliverables/'.$projectMilestone->id);
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
}
