<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Support\PrivateFiles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $request->user()->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'preferences' => array_merge($request->user()->preferences ?? [], [
                'message_notifications' => (bool) ($validated['preferences']['message_notifications'] ?? false),
                'status_notifications' => (bool) ($validated['preferences']['status_notifications'] ?? false),
                'request_notifications' => (bool) ($validated['preferences']['request_notifications'] ?? false),
                'order_notifications' => (bool) ($validated['preferences']['order_notifications'] ?? false),
                'stock_notifications' => (bool) ($validated['preferences']['stock_notifications'] ?? false),
                'newsletter' => (bool) ($validated['preferences']['newsletter'] ?? false),
                'preferred_contact' => $validated['preferences']['preferred_contact'] ?? 'email',
            ]),
        ]);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // La base supprime les demandes en cascade : effacer leurs fichiers avant.
        $user->serviceRequests()->with('milestones:id,service_request_id')->get()
            ->each(function ($serviceRequest): void {
                PrivateFiles::deleteDirectory('service-requests/'.$serviceRequest->id);
                PrivateFiles::deleteDirectory('project-messages/'.$serviceRequest->id);
                $serviceRequest->milestones->each(
                    fn ($milestone) => PrivateFiles::deleteDirectory('project-deliverables/'.$milestone->id)
                );
            });

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
