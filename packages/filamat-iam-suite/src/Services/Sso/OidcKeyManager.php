<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Sso;

use RuntimeException;

class OidcKeyManager
{
    protected ?string $privateKey = null;
    protected ?string $publicKey = null;
    protected ?string $kid = null;

    public function getPrivateKey(): string
    {
        $this->ensureKeys();

        return (string) $this->privateKey;
    }

    public function getPublicKey(): string
    {
        $this->ensureKeys();

        return (string) $this->publicKey;
    }

    public function getKid(): string
    {
        $this->ensureKeys();

        return (string) $this->kid;
    }

    /** @return array<string, mixed> */
    public function jwks(): array
    {
        $this->ensureKeys();

        $publicKey = $this->publicKey;
        if (! $publicKey) {
            return ['keys' => []];
        }

        $details = openssl_pkey_get_details(openssl_pkey_get_public($publicKey));
        if (! is_array($details) || ! isset($details['rsa'])) {
            return ['keys' => []];
        }

        $rsa = $details['rsa'];
        $n = $rsa['n'] ?? null;
        $e = $rsa['e'] ?? null;
        if (! is_string($n) || ! is_string($e)) {
            return ['keys' => []];
        }

        return [
            'keys' => [
                [
                    'kty' => 'RSA',
                    'use' => 'sig',
                    'alg' => 'RS256',
                    'kid' => $this->kid,
                    'n' => $this->base64UrlEncode($n),
                    'e' => $this->base64UrlEncode($e),
                ],
            ],
        ];
    }

    protected function ensureKeys(): void
    {
        if ($this->privateKey && $this->publicKey) {
            return;
        }

        $path = rtrim((string) config('filamat-iam.sso.oidc.key_path', storage_path('app/oidc')), '/');
        if ($path === '') {
            throw new RuntimeException('OIDC key path is missing.');
        }

        if (! is_dir($path) && ! mkdir($path, 0700, true) && ! is_dir($path)) {
            throw new RuntimeException('Unable to create OIDC key directory.');
        }

        $privatePath = $path.'/oidc-private.key';
        $publicPath = $path.'/oidc-public.key';

        if (file_exists($privatePath) && file_exists($publicPath)) {
            $this->privateKey = (string) file_get_contents($privatePath);
            $this->publicKey = (string) file_get_contents($publicPath);
            $this->kid = $this->computeKid($this->publicKey);

            return;
        }

        $resource = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 2048,
        ]);

        if (! $resource) {
            throw new RuntimeException('Unable to generate OIDC keys.');
        }

        $privateKey = '';
        openssl_pkey_export($resource, $privateKey);
        $details = openssl_pkey_get_details($resource);
        if (! is_array($details) || ! isset($details['key'])) {
            throw new RuntimeException('Unable to extract OIDC public key.');
        }

        $publicKey = (string) $details['key'];

        file_put_contents($privatePath, $privateKey);
        @chmod($privatePath, 0600);
        file_put_contents($publicPath, $publicKey);
        @chmod($publicPath, 0644);

        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
        $this->kid = $this->computeKid($publicKey);
    }

    protected function computeKid(string $publicKey): string
    {
        return substr($this->base64UrlEncode(hash('sha256', $publicKey, true)), 0, 32);
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
