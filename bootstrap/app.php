<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Haida\TenancyDomains\Models\SiteDomain;
use Illuminate\Support\Facades\Schema;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustHosts(function (): array {
            $hosts = [];

            $rootDomain = config('tenancy-domains.root_domain');
            if (is_string($rootDomain) && $rootDomain !== '') {
                $hosts[] = '^(.+\\.)?'.preg_quote($rootDomain).'$';
            }

            $allowed = config('tenancy-domains.allowed_hosts', []);
            if (! is_array($allowed)) {
                $allowed = [];
            }

            $appHost = parse_url(config('app.url'), PHP_URL_HOST);
            if (is_string($appHost) && $appHost !== '') {
                $allowed[] = $appHost;
            }

            foreach (array_filter(array_unique($allowed)) as $host) {
                $hosts[] = '^'.preg_quote($host).'$';
            }

            try {
                $table = config('tenancy-domains.tables.site_domains', 'site_domains');
                if (Schema::hasTable($table)) {
                    $domains = SiteDomain::query()
                        ->whereNotNull('verified_at')
                        ->pluck('host')
                        ->all();

                    foreach ($domains as $domain) {
                        $hosts[] = '^'.preg_quote($domain).'$';
                    }
                }
            } catch (\Throwable) {
                // Ignore host enrichment when DB is not ready.
            }

            return array_values(array_unique($hosts));
        }, false);

        $trustedProxies = env('TRUSTED_PROXIES');
        if (is_string($trustedProxies) && $trustedProxies !== '') {
            $proxies = array_filter(array_map('trim', explode(',', $trustedProxies)));
            $trustedList = in_array('*', $proxies, true) ? '*' : $proxies;

            $trustedHeaders = env('TRUSTED_HEADERS');
            $middleware->trustProxies(
                $trustedList,
                is_numeric($trustedHeaders) ? (int) $trustedHeaders : null
            );
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
