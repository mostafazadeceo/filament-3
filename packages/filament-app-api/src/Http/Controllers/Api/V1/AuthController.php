<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Http\Controllers\Api\V1;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAppApi\Services\AppTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController
{
    public function __construct(private readonly AppTokenService $tokenService) {}

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'tenant_id' => ['nullable', 'integer'],
            'integrity_token' => ['nullable', 'string'],
        ]);

        $userModel = config('auth.providers.users.model');
        $user = $userModel::query()->where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'اطلاعات ورود معتبر نیست.',
            ]);
        }

        $tenant = null;
        if (! empty($data['tenant_id'])) {
            $tenant = Tenant::query()->findOrFail($data['tenant_id']);
            if (method_exists($user, 'tenants')) {
                $isMember = $user->tenants()->where('tenants.id', $tenant->getKey())->exists();
                if (! $isMember) {
                    return response()->json(['message' => 'عضویت فضای کاری معتبر نیست.'], 403);
                }
            }
        }

        TenantContext::setTenant($tenant);

        $tokenName = 'app-'.($request->header('X-Device-ID') ?? $request->userAgent() ?? 'client');
        $accessToken = $tenant
            ? $user->createTenantToken($tenant, $tokenName, ['*'])
            : $user->createToken($tokenName, ['*']);

        $refreshToken = $this->tokenService->issueRefreshToken($user, $tenant);

        return response()->json([
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken,
            'user' => [
                'id' => $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
            ],
            'tenant' => $tenant ? ['id' => $tenant->getKey(), 'name' => $tenant->name] : null,
        ]);
    }

    public function refresh(Request $request)
    {
        $data = $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        $refreshToken = $this->tokenService->consumeRefreshToken($data['refresh_token']);
        if (! $refreshToken) {
            return response()->json(['message' => 'توکن معتبر نیست.'], 401);
        }

        $userModel = config('auth.providers.users.model');
        $user = $userModel::query()->findOrFail($refreshToken->user_id);
        $tenant = $refreshToken->tenant_id ? Tenant::query()->find($refreshToken->tenant_id) : null;

        TenantContext::setTenant($tenant);

        $tokenName = 'app-refresh';
        $accessToken = $tenant
            ? $user->createTenantToken($tenant, $tokenName, ['*'])
            : $user->createToken($tokenName, ['*']);

        $nextRefresh = $this->tokenService->issueRefreshToken($user, $tenant);

        return response()->json([
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $nextRefresh,
            'user' => [
                'id' => $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
            ],
            'tenant' => $tenant ? ['id' => $tenant->getKey(), 'name' => $tenant->name] : null,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && method_exists($user, 'currentAccessToken')) {
            $user->currentAccessToken()?->delete();
        }

        return response()->json(['message' => 'خروج انجام شد.']);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'کاربر یافت نشد.'], 404);
        }

        return response()->json([
            'id' => $user->getKey(),
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
