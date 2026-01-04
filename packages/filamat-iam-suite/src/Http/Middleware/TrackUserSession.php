<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Middleware;

use Closure;
use Filamat\IamSuite\Services\SessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackUserSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! (bool) config('filamat-iam.sessions.record', true)) {
            return $next($request);
        }

        $response = $next($request);

        $user = $request->user();
        if (! $user || ! $request->hasSession()) {
            return $response;
        }

        $sessionId = $request->session()->getId();
        if (! $sessionId) {
            return $response;
        }

        $cacheKey = 'iam.session.touch.'.$sessionId;
        if (Cache::has($cacheKey)) {
            return $response;
        }

        Cache::put($cacheKey, true, now()->addMinutes(5));

        app(SessionService::class)->touch($sessionId);

        return $response;
    }
}
