<?php

namespace Haida\TenancyDomains\Middleware;

use Closure;
use Haida\TenancyDomains\Models\SiteDomain;
use Haida\TenancyDomains\Support\SiteContext;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RequireService
{
    public function handle(Request $request, Closure $next, string $service, string $mode = 'loose')
    {
        $current = SiteContext::getService();

        if ($mode === 'strict') {
            if ($current !== $service) {
                throw new NotFoundHttpException('Service not available on this domain.');
            }

            return $next($request);
        }

        if ($current === null || $current === '' || $current === SiteDomain::SERVICE_ALL) {
            return $next($request);
        }

        if ($current === $service) {
            return $next($request);
        }

        throw new NotFoundHttpException('Service not available on this domain.');
    }
}
