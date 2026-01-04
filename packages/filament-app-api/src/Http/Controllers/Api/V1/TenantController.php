<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Http\Controllers\Api\V1;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAppApi\Services\AppTokenService;
use Illuminate\Http\Request;

class TenantController
{
    public function __construct(private readonly AppTokenService $tokenService) {}

    public function current()
    {
        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            return response()->json(['tenant' => null]);
        }

        return response()->json([
            'tenant' => [
                'id' => $tenant->getKey(),
                'name' => $tenant->name,
            ],
        ]);
    }

    public function switch(Request $request)
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'integer'],
        ]);

        $tenant = Tenant::query()->findOrFail($data['tenant_id']);
        $user = $request->user();
        if ($user && method_exists($user, 'tenants')) {
            $isMember = $user->tenants()->where('tenants.id', $tenant->getKey())->exists();
            if (! $isMember) {
                return response()->json(['message' => 'عضویت فضای کاری معتبر نیست.'], 403);
            }
        }

        TenantContext::setTenant($tenant);

        $tokenName = 'app-tenant-switch';
        $accessToken = $user->createTenantToken($tenant, $tokenName, ['*']);
        $refreshToken = $this->tokenService->issueRefreshToken($user, $tenant);

        return response()->json([
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken,
            'tenant' => [
                'id' => $tenant->getKey(),
                'name' => $tenant->name,
            ],
        ]);
    }
}
