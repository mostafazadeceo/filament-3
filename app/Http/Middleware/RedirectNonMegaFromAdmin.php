<?php

namespace App\Http\Middleware;

use Closure;
use Filamat\IamSuite\Support\MegaSuperAdmin;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectNonMegaFromAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || MegaSuperAdmin::check($user)) {
            return $next($request);
        }

        if (! method_exists($user, 'tenants')) {
            return $next($request);
        }

        $tenant = $user->tenants()
            ->wherePivot('status', 'active')
            ->orderBy('tenant_user.created_at')
            ->first();

        if (! $tenant || ! $tenant->slug) {
            return $next($request);
        }

        $target = url('/tenant/' . $tenant->slug);

        if ($request->fullUrl() !== $target) {
            return redirect()->to($target);
        }

        return $next($request);
    }
}
