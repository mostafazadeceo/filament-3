<?php

namespace Haida\TenancyDomains\Middleware;

use Closure;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\TenancyDomains\Models\SiteDomain;
use Haida\TenancyDomains\Support\SiteContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResolveTenantFromHost
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();

        SiteContext::clear();
        TenantContext::setTenant(null);

        $rootDomain = config('tenancy-domains.root_domain');
        if (is_string($rootDomain) && $rootDomain !== '' && $this->isSubdomainOf($host, $rootDomain)) {
            $tenantSlug = $this->extractSubdomain($host, $rootDomain);
            if ($tenantSlug) {
                $tenant = Tenant::query()->where('slug', $tenantSlug)->first();
                if ($tenant) {
                    TenantContext::setTenant($tenant);
                    SiteContext::set($tenant->getKey(), null, $host);

                    return $next($request);
                }
            }
        }

        if ($this->isAllowedHost($host)) {
            return $next($request);
        }

        $domainsTable = (new SiteDomain)->getTable();
        if (! Schema::hasTable($domainsTable)) {
            throw new NotFoundHttpException('Host not recognized.');
        }

        $domain = SiteDomain::query()
            ->where('host', $host)
            ->whereNotNull('verified_at')
            ->first();

        if ($domain) {
            $tenant = Tenant::query()->find($domain->tenant_id);
            if ($tenant) {
                TenantContext::setTenant($tenant);
                SiteContext::set($tenant->getKey(), $domain->site_id, $host);

                return $next($request);
            }
        }

        throw new NotFoundHttpException('Host not recognized.');
    }

    private function isAllowedHost(string $host): bool
    {
        $allowed = config('tenancy-domains.allowed_hosts', []);
        if (! is_array($allowed)) {
            $allowed = [];
        }

        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        if (is_string($appHost) && $appHost !== '') {
            $allowed[] = $appHost;
        }

        return in_array($host, array_unique($allowed), true);
    }

    private function isSubdomainOf(string $host, string $rootDomain): bool
    {
        return $host !== $rootDomain && str_ends_with($host, '.'.$rootDomain);
    }

    private function extractSubdomain(string $host, string $rootDomain): ?string
    {
        if (! $this->isSubdomainOf($host, $rootDomain)) {
            return null;
        }

        $suffix = '.'.$rootDomain;
        $slug = substr($host, 0, -strlen($suffix));

        return $slug !== '' ? $slug : null;
    }
}
