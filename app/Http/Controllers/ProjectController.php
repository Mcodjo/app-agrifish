<?php

namespace App\Http\Controllers;

use App\Models\ProjectMessage;
use App\Models\ProjectMilestone;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Notifications\NewProjectMessage;
use App\Notifications\ServiceRequestActivity;
use App\Support\PrivateFiles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

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
            'events.user',
        ]);

        $milestoneStates = $serviceRequest->milestones->groupBy('status')->map->count();
        $progress = $serviceRequest->milestones->isEmpty()
            ? 0
            : (int) round((($milestoneStates->get('validated', 0) + $milestoneStates->get('completed', 0)) / max($serviceRequest->milestones->count(), 1)) * 100);

        $user = $request->user();

        return view('projects.show', [
            'serviceRequest' => $serviceRequest,
            'progress' => $progress,
            'canManageMilestones' => $user->can('manageMilestones', $serviceRequest),
            'canCancel' => $user->can('cancel', $serviceRequest),
            'canAddDocuments' => $user->can('addDocuments', $serviceRequest),
            'canRemoveDocuments' => $user->can('removeDocuments', $serviceRequest),
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

        $message = ProjectMessage::create([
            'service_request_id' => $serviceRequest->id,
            'sender_id' => $request->user()->id,
            'body' => $validated['body'],
            'attachment_path' => $attachmentPath,
        ]);

        $recipients = $serviceRequest->participantsExcept($request->user())
            ->filter(fn (User $user) => $user->wantsEmail('message_notifications'));

        Notification::send($recipients, new NewProjectMessage($message));

        return redirect()->to(route('projects.show', $serviceRequest).'#messagerie')->with('status', 'message-sent');
    }

    public function cancel(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        Gate::authorize('cancel', $serviceRequest);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $previous = $serviceRequest->status;

        $serviceRequest->forceFill([
            'status' => 'cancelled',
            'status_changed_at' => now(),
        ])->save();

        $serviceRequest->record('cancelled', $request->user(), [
            'from_status' => $previous,
            'to_status' => 'cancelled',
            'details' => $validated['reason'] ?? null,
        ]);

        Notification::send(
            $serviceRequest->participantsExcept($request->user()),
            new ServiceRequestActivity($serviceRequest, 'cancelled')
        );

        return redirect()->route('projects.show', $serviceRequest)->with('status', 'request-cancelled');
    }

    public function addDocuments(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        Gate::authorize('addDocuments', $serviceRequest);

        $existing = $serviceRequest->document_paths ?? [];

        $request->validate([
            'documents' => ['required', 'array', 'max:'.max(1, 10 - count($existing))],
            'documents.*' => ['file', 'max:5120', 'mimes:'.PrivateFiles::ALLOWED_MIMES],
        ], [
            'documents.max' => 'Une demande compte au maximum 10 documents.',
        ]);

        $files = $request->file('documents', []);

        foreach ($files as $document) {
            $existing[] = PrivateFiles::store($document, 'service-requests/'.$serviceRequest->id);
        }

        $serviceRequest->update(['document_paths' => array_values($existing)]);
        $serviceRequest->record('documents_added', $request->user(), ['details' => (string) count($files)]);

        return back()->with('status', 'documents-added');
    }

    public function removeDocument(Request $request, ServiceRequest $serviceRequest, int $documentIndex): RedirectResponse
    {
        Gate::authorize('removeDocuments', $serviceRequest);

        $paths = $serviceRequest->document_paths ?? [];
        abort_unless(isset($paths[$documentIndex]), 404);

        PrivateFiles::delete($paths[$documentIndex]);
        unset($paths[$documentIndex]);

        $serviceRequest->update(['document_paths' => array_values($paths)]);
        $serviceRequest->record('document_removed', $request->user());

        return back()->with('status', 'document-removed');
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
