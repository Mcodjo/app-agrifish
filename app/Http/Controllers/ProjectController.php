<?php

namespace App\Http\Controllers;

use App\Models\ProjectMessage;
use App\Models\ProjectMilestone;
use App\Models\ServiceRequest;
use App\Support\PrivateFiles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function show(Request $request, ServiceRequest $serviceRequest)
    {
        Gate::authorize('view', $serviceRequest);

        $serviceRequest->load([
            'client',
            'assignedExpert',
            'milestones' => function ($query): void {
                $query->orderBy('sort_order')->orderBy('id');
            },
            'messages.sender',
        ]);

        $milestoneStates = $serviceRequest->milestones->groupBy('status')->map->count();
        $progress = $serviceRequest->milestones->isEmpty()
            ? 0
            : (int) round((($milestoneStates->get('validated', 0) + $milestoneStates->get('completed', 0)) / max($serviceRequest->milestones->count(), 1)) * 100);

        return view('projects.show', [
            'serviceRequest' => $serviceRequest,
            'progress' => $progress,
            'canManageMilestones' => $request->user()->can('manageMilestones', $serviceRequest),
            'milestoneStats' => [
                'planned' => $milestoneStates->get('planned', 0),
                'in_progress' => $milestoneStates->get('in_progress', 0),
                'validated' => $milestoneStates->get('validated', 0),
                'blocked' => $milestoneStates->get('blocked', 0),
                'completed' => $milestoneStates->get('completed', 0),
            ],
        ]);
    }

    public function sendMessage(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        Gate::authorize('message', $serviceRequest);

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:4000'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:'.PrivateFiles::ALLOWED_MIMES],
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = PrivateFiles::store($request->file('attachment'), 'project-messages/'.$serviceRequest->id);
        }

        ProjectMessage::create([
            'service_request_id' => $serviceRequest->id,
            'sender_id' => $request->user()->id,
            'body' => $validated['body'],
            'attachment_path' => $attachmentPath,
        ]);

        return back()->with('status', 'message-sent');
    }

    public function downloadDocument(ServiceRequest $serviceRequest, int $documentIndex)
    {
        Gate::authorize('view', $serviceRequest);

        return PrivateFiles::download($serviceRequest->document_paths[$documentIndex] ?? null);
    }

    public function downloadDeliverable(ProjectMilestone $projectMilestone, int $deliverableIndex)
    {
        Gate::authorize('view', $projectMilestone->serviceRequest);

        return PrivateFiles::download($projectMilestone->deliverable_paths[$deliverableIndex] ?? null);
    }

    public function downloadMessageAttachment(ServiceRequest $serviceRequest, ProjectMessage $projectMessage)
    {
        Gate::authorize('view', $serviceRequest);

        abort_unless($projectMessage->service_request_id === $serviceRequest->id, 404);

        return PrivateFiles::download($projectMessage->attachment_path);
    }
}
