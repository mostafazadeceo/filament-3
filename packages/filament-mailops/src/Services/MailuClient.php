<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MailuClient
{
    protected function request(): PendingRequest
    {
        if (! config('filament-mailops.mailu.enabled')) {
            throw new RuntimeException('Mailu API is disabled.');
        }

        $baseUrl = rtrim((string) config('filament-mailops.mailu.base_url'), '/');
        $token = (string) config('filament-mailops.mailu.token');

        if ($baseUrl === '') {
            throw new RuntimeException('Mailu API base URL is missing.');
        }

        if ($token === '') {
            throw new RuntimeException('Mailu API token is missing.');
        }

        $verify = (bool) config('filament-mailops.mailu.verify_tls', true);
        $timeout = (int) config('filament-mailops.mailu.timeout', 15);

        return Http::baseUrl($baseUrl)
            ->acceptJson()
            ->withToken($token)
            ->timeout($timeout)
            ->withOptions(['verify' => $verify]);
    }

    public function createDomain(array $payload): Response
    {
        return $this->request()->post('domain', $payload);
    }

    public function getDomain(string $domain): Response
    {
        return $this->request()->get('domain/'.rawurlencode($domain));
    }

    public function updateDomain(string $domain, array $payload): Response
    {
        return $this->request()->patch('domain/'.rawurlencode($domain), $payload);
    }

    public function createUser(array $payload): Response
    {
        return $this->request()->post('user', $payload);
    }

    public function updateUser(string $email, array $payload): Response
    {
        return $this->request()->patch('user/'.rawurlencode($email), $payload);
    }

    public function createAlias(array $payload): Response
    {
        return $this->request()->post('alias', $payload);
    }

    public function updateAlias(string $email, array $payload): Response
    {
        return $this->request()->patch('alias/'.rawurlencode($email), $payload);
    }
}
