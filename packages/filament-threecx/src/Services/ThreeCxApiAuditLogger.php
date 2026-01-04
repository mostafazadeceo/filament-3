<?php

namespace Haida\FilamentThreeCx\Services;

use Haida\FilamentThreeCx\Models\ThreeCxApiAuditLog;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;

class ThreeCxApiAuditLogger
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function log(ThreeCxInstance $instance, array $data, ?Authenticatable $actor = null): void
    {
        if (! (bool) config('filament-threecx.logging.enabled', true)) {
            return;
        }

        $actor ??= auth()->user();

        $metadata = $data['metadata'] ?? [];
        if (is_array($metadata)) {
            $metadata = $this->sanitizeMetadata($metadata);
        }

        ThreeCxApiAuditLog::create([
            'tenant_id' => $instance->tenant_id,
            'instance_id' => $instance->getKey(),
            'actor_type' => $actor ? $actor::class : null,
            'actor_id' => $actor?->getAuthIdentifier(),
            'api_area' => (string) ($data['api_area'] ?? 'unknown'),
            'method' => (string) ($data['method'] ?? 'GET'),
            'path' => (string) ($data['path'] ?? '/'),
            'status_code' => isset($data['status_code']) ? (int) $data['status_code'] : null,
            'duration_ms' => isset($data['duration_ms']) ? (int) $data['duration_ms'] : null,
            'correlation_id' => (string) ($data['correlation_id'] ?? $this->resolveCorrelationId()),
            'metadata' => $metadata,
        ]);
    }

    protected function resolveCorrelationId(): ?string
    {
        if (! app()->bound('correlation_id')) {
            return null;
        }

        $value = app('correlation_id');

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @return array<string, mixed>
     */
    protected function sanitizeMetadata(array $metadata): array
    {
        $redactRequest = (bool) config('filament-threecx.logging.redact_request_body', true);
        $redactResponse = (bool) config('filament-threecx.logging.redact_response_body', true);

        if ($redactRequest) {
            Arr::forget($metadata, ['request_body', 'request_payload']);
        }

        if ($redactResponse) {
            Arr::forget($metadata, ['response_body', 'response_payload']);
        }

        if (isset($metadata['request_headers']) && is_array($metadata['request_headers'])) {
            unset($metadata['request_headers']['Authorization'], $metadata['request_headers']['authorization']);
        }

        if (isset($metadata['response_headers']) && is_array($metadata['response_headers'])) {
            unset($metadata['response_headers']['Authorization'], $metadata['response_headers']['authorization']);
        }

        return $this->redactArray($metadata);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function redactArray(array $data): array
    {
        $redacted = [];
        $redactKeys = array_map('strtolower', (array) config('filament-threecx.security.redacted_fields', []));

        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), $redactKeys, true)) {
                $redacted[$key] = '[redacted]';

                continue;
            }

            if (is_array($value)) {
                $redacted[$key] = $this->redactArray($value);

                continue;
            }

            $redacted[$key] = $value;
        }

        return $redacted;
    }
}
