<?php

namespace Haida\FilamentThreeCx\Http\Middleware;

use Closure;
use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThreeCxCrmAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! (bool) config('filament-threecx.crm_connector.enabled', false)) {
            return response()->json(['message' => 'اتصال CRM غیرفعال است.'], 403);
        }

        $mode = (string) config('filament-threecx.crm_connector.auth_mode', 'connector_key');

        if ($mode === 'api_key') {
            return app(ApiKeyAuth::class)->handle($request, function (Request $request) use ($next): Response {
                return app(ApiAuth::class)->handle($request, $next);
            });
        }

        $header = (string) config('filament-threecx.crm_connector.connector_key_header', 'X-ThreeCX-Connector-Key');
        $token = (string) $request->header($header);

        if ($token === '') {
            return response()->json(['message' => 'کلید اتصال نامعتبر است.'], 401);
        }

        $hash = hash('sha256', $token);
        $instance = ThreeCxInstance::query()
            ->where('crm_connector_key_hash', $hash)
            ->first();

        if (! $instance || ! $instance->crm_connector_enabled) {
            return response()->json(['message' => 'دسترسی غیرمجاز.'], 403);
        }

        if ($instance->tenant) {
            TenantContext::setTenant($instance->tenant);
        }

        $request->attributes->set('threecx_instance', $instance);

        return $next($request);
    }
}
