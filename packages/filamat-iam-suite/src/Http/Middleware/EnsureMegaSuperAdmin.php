<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Middleware;

use Closure;
use Filamat\IamSuite\Support\MegaSuperAdmin;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMegaSuperAdmin
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if (MegaSuperAdmin::check($user)) {
            return $next($request);
        }

        abort(403);
    }
}
