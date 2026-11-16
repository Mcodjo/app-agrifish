<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Support\PrivateFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceRequestController extends Controller
{
    public function create(Request $request)
    {
        return view('requests.create', [
            'seo' => [
                'title' => 'Nouvelle demande de service',
                'description' => 'Déposez une demande de service avec vos documents pour être accompagné par AgriFish.',
                'url' => route('requests.create'),
            ],
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

        return redirect()->route('dashboard')->with('status', 'service-request-created');
    }
}
