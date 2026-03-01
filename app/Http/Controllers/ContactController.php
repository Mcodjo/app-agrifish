<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'subject'    => ['required', 'string', 'max:200'],
            'message'    => ['required', 'string', 'min:20', 'max:3000'],
            'consent'    => ['accepted'],
        ], [
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required'  => 'Le nom est obligatoire.',
            'email.required'      => 'L\'adresse email est obligatoire.',
            'email.email'         => 'L\'adresse email n\'est pas valide.',
            'subject.required'    => 'Veuillez sélectionner un sujet.',
            'message.required'    => 'Le message est obligatoire.',
            'message.min'         => 'Le message doit contenir au moins 20 caractères.',
            'consent.accepted'    => 'Vous devez accepter le traitement de vos données.',
        ]);

        // Envoi de l'email à AgriFish
        Mail::to(config('mail.contact_address', 'contact@agrifish.africa'))
            ->send(new ContactFormMail($validated));

        // Redirecting to confirmation page
        return redirect()->route('contact.merci');
    }
}
