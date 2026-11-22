<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Notifications\AccountInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $role = in_array($request->query('role'), User::ROLES, true) ? $request->query('role') : null;
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->withCount([
                'serviceRequests',
                'assignedRequests as open_projects_count' => fn ($query) => $query->whereNotIn('status', ServiceRequest::CLOSED_STATUSES),
            ])
            ->when($role, fn ($query) => $query->where('role', $role))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            }))
            ->orderByRaw('suspended_at is not null')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'counts' => User::query()->select('role', DB::raw('count(*) as total'))->groupBy('role')->pluck('total', 'role'),
            'currentRole' => $role,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(User::ROLES)],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            // Mot de passe aléatoire : la personne choisit le sien via le lien envoyé.
            'password' => Str::password(32),
        ]);

        $user->forceFill([
            'role' => $validated['role'],
            'email_verified_at' => now(),
        ])->save();

        $user->notify(new AccountInvitation(Password::broker()->createToken($user)));

        return redirect()->route('admin.users.index')->with('status', 'user-created');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(User::ROLES)],
        ]);

        if ($user->is($request->user()) && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Vous ne pouvez pas retirer votre propre rôle d’administrateur.']);
        }

        if ($user->isExpert() && $validated['role'] !== 'expert' && $this->hasOpenProjects($user)) {
            return back()->withErrors(['role' => $user->name.' suit encore des projets en cours : réassignez-les d’abord.']);
        }

        $user->forceFill(['role' => $validated['role']])->save();

        return back()->with('status', 'user-updated');
    }

    public function toggleSuspension(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['suspension' => 'Vous ne pouvez pas suspendre votre propre compte.']);
        }

        if (! $user->isSuspended() && $user->isExpert() && $this->hasOpenProjects($user)) {
            return back()->withErrors(['suspension' => $user->name.' suit encore des projets en cours : réassignez-les d’abord.']);
        }

        $suspending = ! $user->isSuspended();

        $user->forceFill(['suspended_at' => $suspending ? now() : null])->save();

        // Les sessions ouvertes sont aussi coupées par EnsureAccountIsActive, quel que soit le pilote.
        if ($suspending && config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
        }

        return back()->with('status', $suspending ? 'user-suspended' : 'user-reactivated');
    }

    public function sendPasswordLink(User $user): RedirectResponse
    {
        Password::sendResetLink(['email' => $user->email]);

        return back()->with('status', 'password-link-sent');
    }

    private function hasOpenProjects(User $user): bool
    {
        return $user->assignedRequests()->whereNotIn('status', ServiceRequest::CLOSED_STATUSES)->exists();
    }
}
