<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Sso;

final class OidcClient
{
    /** @param array<int, string> $redirectUris */
    /** @param array<int, string> $scopes */
    public function __construct(
        public readonly string $clientId,
        public readonly string $clientSecret,
        public readonly array $redirectUris,
        public readonly array $scopes,
        public readonly ?int $tenantId = null,
        public readonly ?int $connectionId = null,
        public readonly ?string $name = null,
        public readonly ?string $redirectPrefix = null,
    ) {}

    public function allowsRedirectUri(string $redirectUri): bool
    {
        foreach ($this->redirectUris as $allowed) {
            if ($allowed !== '' && hash_equals($allowed, $redirectUri)) {
                return true;
            }
        }

        if ($this->redirectPrefix && str_starts_with($redirectUri, $this->redirectPrefix)) {
            return true;
        }

        return false;
    }
}
