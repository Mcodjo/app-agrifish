<?php

namespace App\Http\Controllers;

use App\Models\ProjectMessage;
use App\Models\ProjectMilestone;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function show(Request $request, ServiceRequest $serviceRequest)
    {
        $this->authorizeAccess($request, $serviceRequest);

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
        $this->authorizeAccess($request, $serviceRequest);

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:4000'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg'],
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('project-messages/'.$serviceRequest->id, 'public');
        }

        ProjectMessage::create([
            'service_request_id' => $serviceRequest->id,
            'sender_id' => $request->user()->id,
            'body' => $validated['body'],
            'attachment_path' => $attachmentPath,
        ]);

        return back()->with('status', 'message-sent');
    }

    public function downloadDocument(Request $request, ServiceRequest $serviceRequest, int $documentIndex)
    {
        $this->authorizeAccess($request, $serviceRequest);

        $path = $serviceRequest->document_paths[$documentIndex] ?? null;

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path);
    }

    public function downloadDeliverable(Request $request, ProjectMilestone $projectMilestone, int $deliverableIndex)
    {
        $this->authorizeAccess($request, $projectMilestone->serviceRequest);

        $path = $projectMilestone->deliverable_paths[$deliverableIndex] ?? null;

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path);
    }

    protected function authorizeAccess(Request $request, ServiceRequest $serviceRequest): void
    {
        $user = $request->user();

        abort_unless(
            $user && ($user->isAdmin() || $user->isExpert() || $serviceRequest->client_id === $user->id),
            403
        );
    }
}
