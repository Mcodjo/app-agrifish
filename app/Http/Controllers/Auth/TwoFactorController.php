<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\TwoFactorCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $pending = $request->session()->get('two_factor');

        if (! $pending) {
            return redirect()->route('login');
        }

        return view('auth.two-factor', ['email' => User::query()->find($pending['id'])?->email]);
    }

    public function store(Request $request): RedirectResponse
    {
        $pending = $request->session()->get('two_factor');

        if (! $pending) {
            return redirect()->route('login');
        }

        $request->validate(['code' => ['required', 'digits:6']], ['code.digits' => 'Le code comporte 6 chiffres.']);

        $user = User::query()->find($pending['id']);

        if (! $user || $user->isSuspended() || ! TwoFactorCode::verify($user->id, $request->input('code'))) {
            throw ValidationException::withMessages(['code' => 'Code incorrect ou expiré. Demandez un nouveau code si besoin.']);
        }

        $request->session()->forget('two_factor');
        Auth::guard('web')->login($user, (bool) $pending['remember']);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function resend(Request $request): RedirectResponse
    {
        $pending = $request->session()->get('two_factor');
        $user = $pending ? User::query()->find($pending['id']) : null;

        if (! $user) {
            return redirect()->route('login');
        }

        TwoFactorCode::send($user);

        return back()->with('status', 'two-factor-sent');
    }
}
