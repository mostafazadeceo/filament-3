<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Services;

use Filamat\IamSuite\Models\Tenant;
use Haida\FilamentAppApi\Models\AppRefreshToken;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

class AppTokenService
{
    public function issueRefreshToken(Authenticatable $user, ?Tenant $tenant): string
    {
        $plain = Str::random(64);

        AppRefreshToken::create([
            'tenant_id' => $tenant?->getKey(),
            'user_id' => $user->getAuthIdentifier(),
            'token_hash' => hash('sha256', $plain),
            'expires_at' => now()->addMinutes((int) config('filament-app-api.auth.refresh_ttl_minutes', 20160)),
        ]);

        return $plain;
    }

    public function consumeRefreshToken(string $plain): ?AppRefreshToken
    {
        $token = AppRefreshToken::query()
            ->where('token_hash', hash('sha256', $plain))
            ->whereNull('revoked_at')
            ->first();

        if (! $token || $token->expires_at->isPast()) {
            return null;
        }

        $token->forceFill(['revoked_at' => now()])->save();

        return $token;
    }
}
