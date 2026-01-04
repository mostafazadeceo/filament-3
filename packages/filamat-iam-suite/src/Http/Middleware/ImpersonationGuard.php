<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Middleware;

use Closure;
use Filamat\IamSuite\Services\AuditService;
use Filamat\IamSuite\Services\ImpersonationService;
use Filamat\IamSuite\Services\SecurityEventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ImpersonationGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        $service = app(ImpersonationService::class);
        if (! $service->isImpersonating()) {
            return $next($request);
        }

        if ($service->isExpired()) {
            $service->stop('expired');

            return $next($request);
        }

        $path = ltrim($request->path(), '/');
        if ($path === 'filamat-iam/impersonation/stop') {
            return $next($request);
        }

        if ($this->isReadOnly($request) || $service->canWrite()) {
            $this->logImpersonationAction($request, $service);

            return $next($request);
        }

        return response()->json(['message' => 'امپرسونیشن فقط در حالت مشاهده مجاز است.'], 403);
    }

    protected function isReadOnly(Request $request): bool
    {
        return in_array(strtoupper($request->method()), ['GET', 'HEAD', 'OPTIONS'], true);
    }

    protected function logImpersonationAction(Request $request, ImpersonationService $service): void
    {
        if ($this->isReadOnly($request) || ! $service->canWrite()) {
            return;
        }

        $session = $service->currentSession();
        if (! $session) {
            return;
        }

        $cacheKey = 'filamat-iam.impersonation.action.'
            .$session->getKey()
            .'.'.md5($request->method().'|'.$request->path());

        if (! Cache::add($cacheKey, true, now()->addMinute())) {
            return;
        }

        app(AuditService::class)->log('impersonation.action', $session, [
            'method' => $request->method(),
            'path' => $request->path(),
        ], $request->user(), $session->tenant);

        app(SecurityEventService::class)->record('impersonation.action', 'warning', $request->user(), $session->tenant, [
            'impersonation_id' => $session->getKey(),
            'method' => $request->method(),
            'path' => $request->path(),
        ]);
    }
}
