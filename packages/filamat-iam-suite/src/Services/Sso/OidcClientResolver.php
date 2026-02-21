<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Sso;

use Illuminate\Support\Arr;

class OidcClientResolver
{
    public function resolve(string $clientId): ?OidcClient
    {
        $clientId = trim($clientId);
        if ($clientId === '') {
            return null;
        }

        $configured = $this->resolveFromConfig($clientId);
        if ($configured) {
            return $configured;
        }

        return $this->resolveFromChatConnection($clientId);
    }

    protected function resolveFromConfig(string $clientId): ?OidcClient
    {
        $clients = (array) config('filamat-iam.sso.oidc.clients', []);
        $client = $clients[$clientId] ?? null;
        if (! is_array($client)) {
            return null;
        }

        $secret = (string) ($client['client_secret'] ?? '');
        $redirectUris = array_values(array_filter(Arr::wrap($client['redirect_uris'] ?? [])));
        $scopes = array_values(array_filter(Arr::wrap($client['scopes'] ?? [])));

        return new OidcClient(
            $clientId,
            $secret,
            $redirectUris,
            $scopes,
            $client['tenant_id'] ?? null,
            null,
            $client['name'] ?? null,
            null,
        );
    }

    protected function resolveFromChatConnection(string $clientId): ?OidcClient
    {
        if (! class_exists(\Haida\FilamentChat\Models\ChatConnection::class)) {
            return null;
        }

        $connection = \Haida\FilamentChat\Models\ChatConnection::query()
            ->withoutGlobalScopes()
            ->where('oidc_client_id', $clientId)
            ->first();

        if (! $connection) {
            return null;
        }

        $secret = (string) ($connection->oidc_client_secret ?? '');
        $redirectUris = array_values(array_filter(Arr::wrap(data_get($connection->settings, 'oidc_redirect_uris', []))));
        $scopes = array_values(array_filter(explode(' ', (string) ($connection->oidc_scopes ?? ''))));

        $baseUrl = rtrim((string) $connection->base_url, '/');
        $defaultRedirects = [
            $baseUrl.'/_oauth/oidc',
            $baseUrl.'/oauth/oidc',
            $baseUrl.'/oauth/oidc/callback',
        ];

        $redirectUris = $redirectUris !== [] ? $redirectUris : $defaultRedirects;

        return new OidcClient(
            $clientId,
            $secret,
            $redirectUris,
            $scopes,
            $connection->tenant_id,
            $connection->getKey(),
            $connection->name,
            $baseUrl !== '' ? $baseUrl : null,
        );
    }
}
