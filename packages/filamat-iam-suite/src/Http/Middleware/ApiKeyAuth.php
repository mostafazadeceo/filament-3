<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Middleware;

use Closure;
use Filamat\IamSuite\Models\ApiKey;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = (string) config('filamat-iam.api.api_key_header', 'X-Api-Key');
        $token = $request->header($header);

        if ($token) {
            $apiKey = ApiKey::query()
                ->withoutGlobalScopes()
                ->where('token_hash', hash('sha256', $token))
                ->first();

            if (! $apiKey || ($apiKey->expires_at && $apiKey->expires_at->isPast())) {
                return response()->json(['message' => 'کلید نامعتبر است.'], 401);
            }

            if ($apiKey->tenant_id) {
                TenantContext::setTenant($apiKey->tenant);
            }

            $apiKey->forceFill(['last_used_at' => now()])->save();
            $request->attributes->set('api_key', $apiKey);
        }

        return $next($request);
    }
}
