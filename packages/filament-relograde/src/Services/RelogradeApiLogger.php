<?php

namespace Haida\FilamentRelograde\Services;

use Haida\FilamentRelograde\Models\RelogradeApiLog;
use Haida\FilamentRelograde\Models\RelogradeConnection;

class RelogradeApiLogger
{
    public function log(RelogradeConnection $connection, array $data): void
    {
        if (! config('relograde.log_requests', true)) {
            return;
        }

        $headers = $data['request_headers'] ?? [];
        if (array_key_exists('Authorization', $headers)) {
            unset($headers['Authorization']);
        }

        RelogradeApiLog::create([
            'connection_id' => $connection->getKey(),
            'method' => $data['method'] ?? 'GET',
            'url' => $data['url'] ?? null,
            'endpoint_name' => $data['endpoint_name'] ?? null,
            'request_headers' => $headers,
            'request_body' => $data['request_body'] ?? null,
            'response_status' => $data['response_status'] ?? null,
            'response_body' => $data['response_body'] ?? null,
            'duration_ms' => $data['duration_ms'] ?? null,
            'error' => $data['error'] ?? null,
        ]);
    }
}
