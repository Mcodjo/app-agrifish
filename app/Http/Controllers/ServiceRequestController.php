<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_type' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'priority' => ['required', 'in:low,normal,high'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['file', 'max:5120', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg'],
        ]);

        $requestModel = ServiceRequest::create([
            'reference' => 'SRQ-' . Str::upper(Str::random(8)),
            'client_id' => $request->user()->id,
            'service_type' => $validated['service_type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'new',
        ]);

        $documentPaths = [];

        foreach ($request->file('documents', []) as $document) {
            $documentPaths[] = $document->store('service-requests/'.$requestModel->id, 'public');
        }

        $requestModel->update([
            'document_paths' => $documentPaths,
        ]);

        return redirect()->route('dashboard')->with('status', 'service-request-created');
    }
}
