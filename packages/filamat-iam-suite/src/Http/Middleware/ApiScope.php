<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Middleware;

use Closure;
use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ApiScope
{
    public function handle(Request $request, Closure $next, string $scope): Response
    {
        if (! (bool) config('filamat-iam.api.enforce_scopes', true)) {
            return $next($request);
        }

        $permission = $this->resolvePermission($scope, $request->method());

        $apiKey = $request->attributes->get('api_key');
        if ($apiKey && ! $this->abilitiesAllow($apiKey->effectiveScopes(), $permission)) {
            return response()->json(['message' => 'مجوز API کافی نیست.'], 403);
        }

        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $token = $user->currentAccessToken();
            if (method_exists($token, 'can')) {
                if (! $token->can($permission)) {
                    return response()->json(['message' => 'توکن مجاز نیست.'], 403);
                }
            } elseif (! $this->abilitiesAllow($token->abilities ?? [], $permission)) {
                return response()->json(['message' => 'توکن مجاز نیست.'], 403);
            }
        }

        if ($user && ! IamAuthorization::allows($permission, TenantContext::getTenant(), $user)) {
            return response()->json(['message' => 'دسترسی کافی نیست.'], 403);
        }

        return $next($request);
    }

    protected function resolvePermission(string $scope, string $method): string
    {
        if (Str::contains($scope, '.')) {
            return $scope;
        }

        $readMethods = ['GET', 'HEAD', 'OPTIONS'];

        return in_array(strtoupper($method), $readMethods, true)
            ? "{$scope}.view"
            : "{$scope}.manage";
    }

    protected function abilitiesAllow(array $abilities, string $permission): bool
    {
        if ($abilities === []) {
            return false;
        }

        if (in_array('*', $abilities, true)) {
            return true;
        }

        return in_array($permission, $abilities, true);
    }
}
