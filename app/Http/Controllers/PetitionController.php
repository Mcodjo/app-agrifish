<?php

namespace App\Http\Controllers;

use App\Mail\VerifySignature;
use App\Models\Signature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PetitionController extends Controller
{
    public function index(): View
    {
        $count = Signature::counted()->count();

        return view('petition.index', [
            'count' => $count,
            'goal' => Signature::goalFor($count),
            'recent' => Signature::counted()->latest()->limit(12)->get(),
            'cities' => Signature::counted()
                ->selectRaw('city, count(*) as total')
                ->groupBy('city')->orderByDesc('total')->limit(5)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Champ piège : les robots le remplissent, les humains ne le voient pas.
        if ($request->filled('website')) {
            return redirect()->route('petition.thanks');
        }

        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'min:2', 'max:60'],
            'last_name' => ['required', 'string', 'min:2', 'max:60'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'city' => ['required', 'string', 'min:2', 'max:60'],
            'profile' => ['required', Rule::in(array_keys(config('petition.profiles')))],
            'comment' => ['nullable', 'string', 'max:280'],
            'show_name' => ['nullable', 'boolean'],
            'accepts_updates' => ['nullable', 'boolean'],
            'consent' => ['accepted'],
        ], [
            'consent.accepted' => 'Vous devez accepter que vos données soient utilisées pour cette pétition.',
        ]);

        $existing = Signature::where('email', $data['email'])->first();

        if ($existing) {
            if ($existing->isCounted()) {
                return redirect()->route('petition.index')
                    ->with('status', 'Cette adresse e-mail a déjà signé la pétition. Merci pour votre soutien !')
                    ->withFragment('signer');
            }

            $this->sendVerification($existing);

            return redirect()->route('petition.thanks')->with('pending', true);
        }

        $signature = Signature::create([
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
            'email' => $data['email'],
            'city' => trim($data['city']),
            'profile' => $data['profile'],
            'comment' => isset($data['comment']) ? trim($data['comment']) ?: null : null,
            'show_name' => $request->boolean('show_name'),
            'accepts_updates' => $request->boolean('accepts_updates'),
            'ip_hash' => hash('sha256', $request->ip().config('app.key')),
            'verified_at' => config('petition.require_verification') ? null : now(),
        ]);

        if (config('petition.require_verification')) {
            $this->sendVerification($signature);

            return redirect()->route('petition.thanks')->with('pending', true);
        }

        return redirect()->route('petition.thanks')->with('confirmed', true);
    }

    public function thanks(): View
    {
        return view('petition.thanks', ['count' => Signature::counted()->count()]);
    }

    public function verify(Signature $signataire): RedirectResponse
    {
        if ($signataire->verified_at === null) {
            $signataire->forceFill(['verified_at' => now()])->save();
        }

        return redirect()->route('petition.thanks')->with('confirmed', true);
    }

    public function signatures(Request $request): View
    {
        return view('petition.signatures', [
            'count' => Signature::counted()->count(),
            'signatures' => Signature::counted()->latest()->paginate(48),
        ]);
    }

    private function sendVerification(Signature $signature): void
    {
        // Au plus un e-mail de confirmation toutes les 10 minutes par adresse.
        if (! Cache::add('verify-mail:'.$signature->id, true, now()->addMinutes(10))) {
            return;
        }

        $url = URL::temporarySignedRoute(
            'petition.verify',
            now()->addDays(config('petition.verification_days')),
            ['signataire' => $signature->id],
        );

        Mail::to($signature->email)->send(new VerifySignature($signature, $url));
    }
}
