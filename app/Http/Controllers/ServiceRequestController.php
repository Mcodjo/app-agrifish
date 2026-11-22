<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\User;
use App\Notifications\ServiceRequestActivity;
use App\Support\PrivateFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class ServiceRequestController extends Controller
{
    public function create(Request $request)
    {
        return view('requests.create', [
            'user' => $request->user(),
            'serviceTypes' => ServiceRequest::SERVICE_TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_type' => ['required', Rule::in(array_keys(ServiceRequest::SERVICE_TYPES))],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'priority' => ['required', 'in:low,normal,high'],
            'documents' => ['nullable', 'array', 'max:10'],
            'documents.*' => ['file', 'max:5120', 'mimes:'.PrivateFiles::ALLOWED_MIMES],
        ]);

        $requestModel = ServiceRequest::create([
            'reference' => ServiceRequest::generateReference(),
            'client_id' => $request->user()->id,
            'service_type' => $validated['service_type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'new',
        ]);

        $documentPaths = [];

        foreach ($request->file('documents', []) as $document) {
            $documentPaths[] = PrivateFiles::store($document, 'service-requests/'.$requestModel->id);
        }

        $requestModel->update([
            'document_paths' => $documentPaths,
        ]);

        $requestModel->record('created', $request->user(), ['to_status' => 'new']);

        $admins = User::query()->where('role', 'admin')->whereNull('suspended_at')->get()
            ->filter(fn (User $admin) => $admin->wantsEmail('request_notifications'));

        Notification::send($admins, new ServiceRequestActivity($requestModel, 'created'));

        return redirect()->route('projects.show', $requestModel)->with('status', 'service-request-created');
    }
}
