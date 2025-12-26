<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Middleware;

use Closure;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenantHeader = (string) config('filamat-iam.api.tenant_header', 'X-Tenant-ID');
        $tenantId = $request->header($tenantHeader) ?? $request->query('tenant_id');

        if (! $tenantId && $request->user() && $request->user()->currentAccessToken()) {
            $abilities = $request->user()->currentAccessToken()->abilities ?? [];
            foreach ($abilities as $ability) {
                if (is_string($ability) && str_starts_with($ability, 'tenant:')) {
                    $tenantId = (int) str_replace('tenant:', '', $ability);
                    break;
                }
            }
        }

        if ($tenantId) {
            $tenant = Tenant::query()->find($tenantId);
            if ($tenant) {
                TenantContext::setTenant($tenant);
            }
        }

        return $next($request);
    }
}
