<?php

namespace Haida\FilamentThreeCx\Services;

use Haida\FilamentThreeCx\Contracts\ThreeCxTokenProviderInterface;
use Haida\FilamentThreeCx\Exceptions\ThreeCxAuthException;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Illuminate\Support\Carbon;

class ThreeCxAuthService
{
    public function __construct(
        protected ThreeCxTokenProviderInterface $tokenProvider,
        protected ThreeCxHttp $http,
    ) {}

    public function getAccessToken(ThreeCxInstance $instance, string $scope, bool $forceRefresh = false): string
    {
        if (! $forceRefresh) {
            $cached = $this->tokenProvider->getToken($instance, $scope);
            if ($cached && ! empty($cached['access_token'])) {
                return (string) $cached['access_token'];
            }
        }

        $tokenData = $this->requestToken($instance, $scope);
        $accessToken = (string) ($tokenData['access_token'] ?? '');
        if ($accessToken === '') {
            throw ThreeCxAuthException::authFailed();
        }

        $expiresIn = (int) ($tokenData['expires_in'] ?? 3600);
        $expiresAt = Carbon::now()->addSeconds(max(60, $expiresIn))->subSeconds(30);

        $this->tokenProvider->storeToken($instance, $scope, $accessToken, $expiresAt);

        return $accessToken;
    }

    protected function requestToken(ThreeCxInstance $instance, string $scope): array
    {
        if (! $instance->client_id || ! $instance->client_secret) {
            throw ThreeCxAuthException::authFailed();
        }

        $tokenPath = (string) config('filament-threecx.auth.token_path', '/connect/token');
        $grantType = (string) config('filament-threecx.auth.grant_type', 'client_credentials');
        $clientAuth = (string) config('filament-threecx.auth.client_auth', 'basic');

        $payload = [
            'grant_type' => $grantType,
        ];

        if ($scope !== '') {
            $payload['scope'] = $scope;
        }

        $pending = $this->http->pendingRequest($instance)->asForm();

        if ($clientAuth === 'basic') {
            $pending = $pending->withBasicAuth((string) $instance->client_id, (string) $instance->client_secret);
        } else {
            $payload['client_id'] = (string) $instance->client_id;
            $payload['client_secret'] = (string) $instance->client_secret;
        }

        $url = $this->http->buildUrl((string) $instance->base_url, $tokenPath);
        $response = $pending->post($url, $payload);

        if (! $response->successful()) {
            throw ThreeCxAuthException::authFailed($response->status(), $response->json());
        }

        $data = $response->json();
        if (! is_array($data)) {
            throw ThreeCxAuthException::authFailed($response->status(), null);
        }

        return $data;
    }
}
