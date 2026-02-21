<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Middleware;

use Closure;
use Filamat\IamSuite\Support\MegaSuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
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

        // Non-mega users can still authenticate (shared guard), but must use the tenant panel.
        //
        // Important: Filament/Livewire may navigate with XHR/fetch (Accept: */* + X-Requested-With),
        // which makes `$request->expectsJson()` true even for GET requests. If we abort(403) there,
        // users see a confusing "Forbidden" after a successful login on `/admin/login`.
        //
        // For safe UX, always redirect GET/HEAD to the tenant panel. For non-GET requests, keep
        // JSON callers on a 403 to avoid hiding authorization issues.
        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            /** @var RedirectResponse $response */
            $response = redirect()->to('/tenant');

            return $response;
        }

        if ($request->expectsJson()) {
            /** @var JsonResponse $response */
            $response = response()->json([
                'message' => 'Forbidden',
                'redirect' => '/tenant',
            ], 403);

            return $response;
        }

        /** @var RedirectResponse $response */
        $response = redirect()->to('/tenant');

        return $response;
    }
}
