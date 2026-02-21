<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Sso;

use App\Models\User;
use Filamat\IamSuite\Models\OidcAuthCode;
use Filamat\IamSuite\Models\OidcRefreshToken;
use Filamat\IamSuite\Models\Tenant;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;

class OidcService
{
    public function __construct(
        private readonly OidcClientResolver $clients,
        private readonly OidcJwtService $jwt,
    ) {}

    public function enabled(): bool
    {
        return (bool) config('filamat-iam.sso.enabled', false);
    }

    public function issuer(): string
    {
        $issuer = (string) config('filamat-iam.sso.oidc.issuer', config('app.url', ''));
        return rtrim($issuer, '/');
    }

    public function loginUrl(): string
    {
        return (string) config('filamat-iam.sso.oidc.login_url', '/admin/login');
    }

    public function resolveLoginUrl(OidcClient $client): string
    {
        $url = $this->loginUrl();
        if (! str_contains($url, '{tenant}')) {
            return $url;
        }

        if (! $client->tenantId) {
            return str_replace('{tenant}', '', $url);
        }

        $tenant = Tenant::query()->find($client->tenantId);
        if (! $tenant) {
            return str_replace('{tenant}', '', $url);
        }

        return str_replace('{tenant}', (string) $tenant->slug, $url);
    }

    public function authorizePath(): string
    {
        return (string) config('filamat-iam.sso.oidc.authorize_path', '/oidc/authorize');
    }

    public function tokenPath(): string
    {
        return (string) config('filamat-iam.sso.oidc.token_path', '/oidc/token');
    }

    public function userinfoPath(): string
    {
        return (string) config('filamat-iam.sso.oidc.userinfo_path', '/oidc/userinfo');
    }

    public function jwksPath(): string
    {
        return (string) config('filamat-iam.sso.oidc.jwks_path', '/oidc/jwks.json');
    }

    public function endpointUrl(string $path): string
    {
        if ($path === '') {
            return $this->issuer();
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return $this->issuer().$path;
    }

    /** @return array<int, string> */
    public function allowedScopes(): array
    {
        $scopes = Arr::wrap(config('filamat-iam.sso.oidc.allowed_scopes', ['openid', 'profile', 'email']));
        $scopes = array_values(array_unique(array_filter(array_map('strval', $scopes))));

        if (! in_array('openid', $scopes, true)) {
            array_unshift($scopes, 'openid');
        }

        return $scopes;
    }

    public function resolveClient(string $clientId): ?OidcClient
    {
        return $this->clients->resolve($clientId);
    }

    /** @param array<int, string> $scopes */
    public function normalizeScopes(array $scopes): array
    {
        $allowed = $this->allowedScopes();
        $allowedMap = array_fill_keys($allowed, true);
        $scopes = array_values(array_unique(array_filter($scopes)));

        $filtered = array_values(array_filter($scopes, fn (string $scope) => isset($allowedMap[$scope])));
        if (! in_array('openid', $filtered, true)) {
            $filtered[] = 'openid';
        }

        return $filtered;
    }

    public function issueAuthorizationCode(
        OidcClient $client,
        User $user,
        string $redirectUri,
        array $scopes,
        ?string $nonce,
    ): string {
        $code = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $hash = hash('sha256', $code);

        $ttl = (int) config('filamat-iam.sso.oidc.code_ttl_seconds', 300);

        OidcAuthCode::create([
            'client_id' => $client->clientId,
            'user_id' => $user->getAuthIdentifier(),
            'tenant_id' => $client->tenantId,
            'redirect_uri' => $redirectUri,
            'scope' => implode(' ', $scopes),
            'nonce' => $nonce,
            'code_hash' => $hash,
            'expires_at' => now()->addSeconds($ttl),
        ]);

        return $code;
    }

    public function consumeAuthorizationCode(string $code, OidcClient $client, string $redirectUri): ?OidcAuthCode
    {
        $hash = hash('sha256', $code);

        $authCode = OidcAuthCode::query()
            ->where('client_id', $client->clientId)
            ->where('code_hash', $hash)
            ->whereNull('consumed_at')
            ->first();

        if (! $authCode) {
            return null;
        }

        if ($authCode->expires_at && $authCode->expires_at->isPast()) {
            return null;
        }

        if (! hash_equals((string) $authCode->redirect_uri, $redirectUri)) {
            return null;
        }

        $authCode->forceFill(['consumed_at' => now()])->save();

        return $authCode;
    }

    /** @param array<int, string> $scopes */
    /** @return array<string, mixed> */
    public function issueTokens(User $user, OidcClient $client, array $scopes, ?string $nonce = null): array
    {
        $now = now();
        $tokenTtl = (int) config('filamat-iam.sso.oidc.token_ttl_seconds', 3600);
        $refreshTtl = (int) config('filamat-iam.sso.oidc.refresh_ttl_seconds', 2592000);

        $tenantId = $client->tenantId;
        if ($tenantId && method_exists($user, 'tenants')) {
            $activeMembership = $user->tenants()
                ->where('tenants.id', $tenantId)
                ->wherePivotIn('status', ['active', 'invited'])
                ->exists();
            if (! $activeMembership) {
                throw new RuntimeException('User is not a member of this tenant.');
            }
        }

        $claims = $this->buildClaims($user, $client, $scopes);
        $accessClaims = array_merge($claims, [
            'iss' => $this->issuer(),
            'aud' => $client->clientId,
            'iat' => $now->timestamp,
            'exp' => $now->copy()->addSeconds($tokenTtl)->timestamp,
            'scope' => implode(' ', $scopes),
            'token_use' => 'access',
        ]);

        $idClaims = array_merge($claims, [
            'iss' => $this->issuer(),
            'aud' => $client->clientId,
            'iat' => $now->timestamp,
            'exp' => $now->copy()->addSeconds($tokenTtl)->timestamp,
            'token_use' => 'id',
        ]);
        if ($nonce !== null) {
            $idClaims['nonce'] = $nonce;
        }

        $accessToken = $this->jwt->encode($accessClaims);
        $idToken = $this->jwt->encode($idClaims);

        $refreshToken = $this->issueRefreshToken($user, $client, $scopes, $refreshTtl);

        return [
            'access_token' => $accessToken,
            'id_token' => $idToken,
            'token_type' => 'Bearer',
            'expires_in' => $tokenTtl,
            'scope' => implode(' ', $scopes),
            'refresh_token' => $refreshToken,
        ];
    }

    /** @return array<string, mixed> */
    public function decodeAccessToken(string $token): array
    {
        $payload = $this->jwt->decode($token);
        if (($payload['iss'] ?? null) !== $this->issuer()) {
            throw new RuntimeException('Invalid issuer.');
        }

        $exp = $payload['exp'] ?? null;
        if ($exp && is_numeric($exp) && (int) $exp < time()) {
            throw new RuntimeException('Token expired.');
        }

        return $payload;
    }

    /** @param array<int, string> $scopes */
    protected function issueRefreshToken(User $user, OidcClient $client, array $scopes, int $ttl): string
    {
        $plain = rtrim(strtr(base64_encode(random_bytes(48)), '+/', '-_'), '=');
        $hash = hash('sha256', $plain);

        OidcRefreshToken::create([
            'client_id' => $client->clientId,
            'user_id' => $user->getAuthIdentifier(),
            'tenant_id' => $client->tenantId,
            'scope' => implode(' ', $scopes),
            'token_hash' => $hash,
            'expires_at' => now()->addSeconds($ttl),
        ]);

        return $plain;
    }

    public function consumeRefreshToken(string $token, OidcClient $client): ?OidcRefreshToken
    {
        $hash = hash('sha256', $token);

        $refresh = OidcRefreshToken::query()
            ->where('client_id', $client->clientId)
            ->where('token_hash', $hash)
            ->whereNull('revoked_at')
            ->first();

        if (! $refresh) {
            return null;
        }

        if ($refresh->expires_at && $refresh->expires_at->isPast()) {
            return null;
        }

        $refresh->forceFill(['revoked_at' => now()])->save();

        return $refresh;
    }

    /** @param array<int, string> $scopes */
    /** @return array<string, mixed> */
    protected function buildClaims(User $user, OidcClient $client, array $scopes): array
    {
        $claims = [
            'sub' => (string) $user->getAuthIdentifier(),
        ];

        if (in_array('profile', $scopes, true)) {
            $claims['name'] = (string) $user->name;
            $claims['preferred_username'] = $this->preferredUsername($user);
        }

        if (in_array('email', $scopes, true)) {
            $claims['email'] = (string) $user->email;
            $emailVerified = (bool) ($user->email_verified_at ?? false);

            // Rocket.Chat blocks OAuth logins (and may force a broken "reset password" flow)
            // when `email_verified` is false, even if the user is authenticated via OIDC.
            // Treat SSO-authenticated emails as verified for Rocket.Chat clients.
            if (str_starts_with($client->clientId, 'rocketchat-')) {
                $emailVerified = true;
            }

            $claims['email_verified'] = $emailVerified;
        }

        if ($client->tenantId) {
            $claims['tenant_id'] = $client->tenantId;
        }

        if (in_array('roles', $scopes, true) && method_exists($user, 'roles') && $client->tenantId) {
            $claims['roles'] = $this->resolveRoles($user, $client->tenantId);
        }

        return $claims;
    }

    protected function preferredUsername(User $user): string
    {
        $id = (string) $user->getAuthIdentifier();

        // Keep `preferred_username` stable + unique across tenants and email domains.
        // This also aligns with Rocket.Chat provisioning usernames like `prefix.{user_id}`.
        if ($user->email) {
            $prefix = Str::slug(Str::before((string) $user->email, '@'));
            $prefix = $prefix !== '' ? $prefix : 'user';

            return $prefix.'.'.$id;
        }

        $name = Str::slug((string) $user->name);
        $name = $name !== '' ? $name : 'user';

        return $name.'.'.$id;
    }

    /** @return array<int, string> */
    protected function resolveRoles(User $user, int $tenantId): array
    {
        try {
            if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenantId);
            }
        } catch (\Throwable) {
            // Ignore
        }

        if (! method_exists($user, 'getRoleNames')) {
            return [];
        }

        return $user->getRoleNames()->values()->all();
    }

    public function resolveTenantFromClient(OidcClient $client): ?Tenant
    {
        if (! $client->tenantId) {
            return null;
        }

        return Tenant::query()->find($client->tenantId);
    }
}
