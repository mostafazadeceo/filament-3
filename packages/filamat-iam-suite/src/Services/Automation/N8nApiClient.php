<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Automation;

use Illuminate\Support\Facades\Http;

class N8nApiClient
{
    /**
     * @return array{enabled: bool, status: string, code?: int|null}
     */
    public function health(): array
    {
        if (! (bool) config('filamat-iam.automation.n8n_api.enabled', false)) {
            return ['enabled' => false, 'status' => 'disabled'];
        }

        $baseUrl = rtrim((string) config('filamat-iam.automation.n8n_api.base_url', ''), '/');
        $apiKey = (string) config('filamat-iam.automation.n8n_api.api_key', '');
        $endpoint = (string) config('filamat-iam.automation.n8n_api.health_endpoint', '/healthz');

        if ($baseUrl === '' || $apiKey === '') {
            return ['enabled' => true, 'status' => 'misconfigured'];
        }

        $response = Http::timeout(10)
            ->withHeaders(['X-N8N-API-KEY' => $apiKey])
            ->get($baseUrl.$endpoint);

        return [
            'enabled' => true,
            'status' => $response->successful() ? 'ok' : 'error',
            'code' => $response->status(),
        ];
    }
}
