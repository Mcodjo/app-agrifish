<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $password = (string) config('petition.admin_password');

        if ($password === '') {
            abort(404);
        }

        $userOk = hash_equals((string) config('petition.admin_user'), (string) $request->getUser());
        $passOk = hash_equals($password, (string) $request->getPassword());

        if (! $userOk || ! $passOk) {
            return response('Accès réservé.', 401, ['WWW-Authenticate' => 'Basic realm="Administration pétition"']);
        }

        return $next($request);
    }
}
