<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Oidc;

use App\Models\User;
use Filamat\IamSuite\Services\Sso\OidcKeyManager;
use Filamat\IamSuite\Services\Sso\OidcService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OidcController extends Controller
{
    public function configuration(OidcService $oidc): JsonResponse
    {
        if (! $oidc->enabled()) {
            return response()->json(['message' => 'SSO is disabled.'], 501);
        }

        return response()->json([
            'issuer' => $oidc->issuer(),
            'authorization_endpoint' => $oidc->endpointUrl($oidc->authorizePath()),
            'token_endpoint' => $oidc->endpointUrl($oidc->tokenPath()),
            'userinfo_endpoint' => $oidc->endpointUrl($oidc->userinfoPath()),
            'jwks_uri' => $oidc->endpointUrl($oidc->jwksPath()),
            'response_types_supported' => ['code'],
            'subject_types_supported' => ['public'],
            'id_token_signing_alg_values_supported' => ['RS256'],
            'scopes_supported' => $oidc->allowedScopes(),
        ]);
    }

    public function jwks(OidcService $oidc, OidcKeyManager $keys): JsonResponse
    {
        if (! $oidc->enabled()) {
            return response()->json(['message' => 'SSO is disabled.'], 501);
        }

        return response()->json($keys->jwks());
    }

    public function authorize(Request $request, OidcService $oidc): RedirectResponse|JsonResponse
    {
        if (! $oidc->enabled()) {
            return response()->json(['message' => 'SSO is disabled.'], 501);
        }

        $clientId = (string) $request->query('client_id', '');
        $redirectUri = (string) $request->query('redirect_uri', '');
        $responseType = (string) $request->query('response_type', '');
        $scope = (string) $request->query('scope', '');
        $state = (string) $request->query('state', '');
        $nonce = $request->query('nonce');

        if ($clientId === '' || $redirectUri === '') {
            return $this->errorResponse('invalid_request', 'Missing client_id or redirect_uri.');
        }

        $client = $oidc->resolveClient($clientId);
        if (! $client) {
            return $this->errorResponse('invalid_client', 'Unknown client.');
        }

        if (! $client->allowsRedirectUri($redirectUri)) {
            return $this->errorResponse('invalid_request', 'Invalid redirect_uri.');
        }

        if ($responseType !== 'code') {
            return $this->redirectError($redirectUri, $state, 'unsupported_response_type', 'Only authorization code is supported.');
        }

        $scopes = $scope !== '' ? preg_split('/\s+/', trim($scope)) : [];
        $scopes = is_array($scopes) ? $scopes : [];
        $scopes = $oidc->normalizeScopes($scopes);
        if ($client->scopes !== []) {
            $allowed = array_fill_keys($client->scopes, true);
            $scopes = array_values(array_filter($scopes, fn (string $value) => isset($allowed[$value])));
        }

        if (! in_array('openid', $scopes, true)) {
            return $this->redirectError($redirectUri, $state, 'invalid_scope', 'openid scope is required.');
        }

        if (! auth()->check()) {
            return redirect()->guest($oidc->resolveLoginUrl($client));
        }

        $user = $request->user();
        if (! $user instanceof User) {
            return $this->redirectError($redirectUri, $state, 'access_denied', 'Invalid user session.');
        }

        $tenant = $oidc->resolveTenantFromClient($client);
        if ($tenant && method_exists($user, 'tenants')) {
            $allowed = $user->tenants()
                ->where('tenants.id', $tenant->getKey())
                ->wherePivotIn('status', ['active', 'invited'])
                ->exists();
            if (! $allowed) {
                return $this->redirectError($redirectUri, $state, 'access_denied', 'User is not a member of this tenant.');
            }
        }

        // Enforce "chat enabled" users only for the shared Rocket.Chat OIDC client.
        // Without this, Rocket.Chat could auto-provision Hub users that shouldn't have chat access.
        $enforceChatAccess = (bool) config('filamat-iam.chat.enforce_oidc_access', false);
        $chatClientId = (string) config('filamat-iam.chat.oidc_client_id', '');
        if ($enforceChatAccess && $chatClientId !== '' && hash_equals($client->clientId, $chatClientId)) {
            if (class_exists(\Haida\FilamentChat\Models\ChatUserLink::class)) {
                $hasActiveLink = \Haida\FilamentChat\Models\ChatUserLink::query()
                    ->where('user_id', $user->getAuthIdentifier())
                    ->where('status', 'active')
                    ->exists();

                if (! $hasActiveLink) {
                    return $this->redirectError($redirectUri, $state, 'access_denied', 'Chat access is not enabled for this user.');
                }
            }
        }

        try {
            $code = $oidc->issueAuthorizationCode(
                $client,
                $user,
                $redirectUri,
                $scopes,
                is_string($nonce) ? $nonce : null,
            );
        } catch (\Throwable $exception) {
            return $this->redirectError($redirectUri, $state, 'server_error', $exception->getMessage());
        }

        $query = ['code' => $code];
        if ($state !== '') {
            $query['state'] = $state;
        }

        return redirect()->to($this->appendQuery($redirectUri, $query));
    }

    public function token(Request $request, OidcService $oidc): JsonResponse
    {
        if (! $oidc->enabled()) {
            return response()->json(['message' => 'SSO is disabled.'], 501);
        }

        $grantType = (string) $request->input('grant_type', '');

        $client = $this->authenticateClient($request, $oidc);
        if (! $client) {
            return $this->errorResponse('invalid_client', 'Invalid client credentials.', 401);
        }

        if ($grantType === 'authorization_code') {
            $code = (string) $request->input('code', '');
            $redirectUri = (string) $request->input('redirect_uri', '');

            if ($code === '' || $redirectUri === '') {
                return $this->errorResponse('invalid_request', 'Missing code or redirect_uri.');
            }

            $authCode = $oidc->consumeAuthorizationCode($code, $client, $redirectUri);
            if (! $authCode) {
                return $this->errorResponse('invalid_grant', 'Invalid authorization code.');
            }

            $user = User::query()->find($authCode->user_id);
            if (! $user) {
                return $this->errorResponse('invalid_grant', 'User not found.');
            }

            $scopes = $authCode->scope ? preg_split('/\s+/', trim((string) $authCode->scope)) : [];
            $scopes = is_array($scopes) ? $scopes : [];

            $payload = $oidc->issueTokens($user, $client, $scopes, $authCode->nonce);

            return response()->json($payload);
        }

        if ($grantType === 'refresh_token') {
            $refreshToken = (string) $request->input('refresh_token', '');
            if ($refreshToken === '') {
                return $this->errorResponse('invalid_request', 'Missing refresh_token.');
            }

            $refresh = $oidc->consumeRefreshToken($refreshToken, $client);
            if (! $refresh) {
                return $this->errorResponse('invalid_grant', 'Invalid refresh token.');
            }

            $user = User::query()->find($refresh->user_id);
            if (! $user) {
                return $this->errorResponse('invalid_grant', 'User not found.');
            }

            $scopes = $refresh->scope ? preg_split('/\s+/', trim((string) $refresh->scope)) : [];
            $scopes = is_array($scopes) ? $scopes : [];

            $payload = $oidc->issueTokens($user, $client, $scopes, null);

            return response()->json($payload);
        }

        return $this->errorResponse('unsupported_grant_type', 'Unsupported grant type.');
    }

    public function userinfo(Request $request, OidcService $oidc): JsonResponse
    {
        if (! $oidc->enabled()) {
            return response()->json(['message' => 'SSO is disabled.'], 501);
        }

        $token = $this->resolveBearerToken($request);
        if (! $token) {
            return $this->errorResponse('invalid_request', 'Missing access token.', 401);
        }

        try {
            $payload = $oidc->decodeAccessToken($token);
        } catch (\Throwable $exception) {
            return $this->errorResponse('invalid_token', $exception->getMessage(), 401);
        }

        $scope = (string) ($payload['scope'] ?? '');
        $scopes = $scope !== '' ? preg_split('/\s+/', trim($scope)) : [];
        $scopes = is_array($scopes) ? $scopes : [];

        // CustomOAuth providers (e.g., Rocket.Chat "Custom OAuth") often expect `id` + `email` + `username`.
        // Keep `sub` for OIDC compliance, and also provide common aliases for broad compatibility.
        $sub = (string) ($payload['sub'] ?? '');
        $response = [
            'sub' => $sub,
            'id' => $sub,
        ];

        if (in_array('profile', $scopes, true)) {
            if (isset($payload['name'])) {
                $response['name'] = $payload['name'];
            }
            if (isset($payload['preferred_username'])) {
                $response['preferred_username'] = $payload['preferred_username'];
                $response['username'] = $payload['preferred_username'];
            }
        }

        if (in_array('email', $scopes, true) && isset($payload['email'])) {
            $response['email'] = $payload['email'];
            $response['email_verified'] = $payload['email_verified'] ?? false;
        }

        if (isset($payload['tenant_id'])) {
            $response['tenant_id'] = $payload['tenant_id'];
        }

        if (in_array('roles', $scopes, true) && isset($payload['roles'])) {
            $response['roles'] = $payload['roles'];
        }

        return response()->json($response);
    }

    protected function authenticateClient(Request $request, OidcService $oidc): ?\Filamat\IamSuite\Services\Sso\OidcClient
    {
        $authHeader = (string) $request->header('Authorization', '');
        $clientId = '';
        $clientSecret = '';

        if (str_starts_with($authHeader, 'Basic ')) {
            $encoded = substr($authHeader, 6);
            $decoded = base64_decode($encoded, true) ?: '';
            if (str_contains($decoded, ':')) {
                [$clientId, $clientSecret] = explode(':', $decoded, 2);
            }
        }

        if ($clientId === '') {
            $clientId = (string) $request->input('client_id', '');
            $clientSecret = (string) $request->input('client_secret', '');
        }

        if ($clientId === '') {
            return null;
        }

        $client = $oidc->resolveClient($clientId);
        if (! $client) {
            return null;
        }

        if ($client->clientSecret !== '' && ! hash_equals($client->clientSecret, $clientSecret)) {
            return null;
        }

        return $client;
    }

    protected function resolveBearerToken(Request $request): ?string
    {
        $header = (string) $request->header('Authorization', '');
        if (str_starts_with($header, 'Bearer ')) {
            return trim(substr($header, 7));
        }

        $token = (string) $request->input('access_token', '');
        return $token !== '' ? $token : null;
    }

    protected function errorResponse(string $error, string $description, int $status = 400): JsonResponse
    {
        return response()->json([
            'error' => $error,
            'error_description' => $description,
        ], $status);
    }

    protected function redirectError(string $redirectUri, string $state, string $error, string $description): RedirectResponse
    {
        $query = [
            'error' => $error,
            'error_description' => $description,
        ];
        if ($state !== '') {
            $query['state'] = $state;
        }

        return redirect()->to($this->appendQuery($redirectUri, $query));
    }

    /** @param array<string, string> $query */
    protected function appendQuery(string $url, array $query): string
    {
        $separator = str_contains($url, '?') ? '&' : '?';

        return $url.$separator.http_build_query($query);
    }
}
