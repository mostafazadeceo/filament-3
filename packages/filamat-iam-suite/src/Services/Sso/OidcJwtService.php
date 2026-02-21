<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Sso;

use RuntimeException;

class OidcJwtService
{
    public function __construct(private readonly OidcKeyManager $keys) {}

    /** @param array<string, mixed> $claims */
    /** @param array<string, mixed> $headers */
    public function encode(array $claims, array $headers = []): string
    {
        $header = array_merge([
            'typ' => 'JWT',
            'alg' => 'RS256',
            'kid' => $this->keys->getKid(),
        ], $headers);

        $segments = [
            $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),
            $this->base64UrlEncode(json_encode($claims, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),
        ];

        $signingInput = implode('.', $segments);
        $signature = '';
        $privateKey = $this->keys->getPrivateKey();

        if (! openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Unable to sign OIDC token.');
        }

        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    /** @return array<string, mixed> */
    public function decode(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new RuntimeException('Invalid token format.');
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;
        $header = json_decode($this->base64UrlDecode($encodedHeader), true);
        if (! is_array($header) || ($header['alg'] ?? '') !== 'RS256') {
            throw new RuntimeException('Unsupported token algorithm.');
        }

        $payload = json_decode($this->base64UrlDecode($encodedPayload), true);
        if (! is_array($payload)) {
            throw new RuntimeException('Invalid token payload.');
        }

        $signature = $this->base64UrlDecode($encodedSignature);
        $publicKey = $this->keys->getPublicKey();

        $verified = openssl_verify($encodedHeader.'.'.$encodedPayload, $signature, $publicKey, OPENSSL_ALGO_SHA256);
        if ($verified !== 1) {
            throw new RuntimeException('Token signature verification failed.');
        }

        return $payload;
    }

    protected function base64UrlEncode(string|false $data): string
    {
        if ($data === false) {
            return '';
        }

        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected function base64UrlDecode(string $data): string
    {
        $padded = strtr($data, '-_', '+/');
        $padLength = 4 - (strlen($padded) % 4);
        if ($padLength < 4) {
            $padded .= str_repeat('=', $padLength);
        }

        $decoded = base64_decode($padded, true);
        if ($decoded === false) {
            throw new RuntimeException('Invalid base64 value.');
        }

        return $decoded;
    }
}
