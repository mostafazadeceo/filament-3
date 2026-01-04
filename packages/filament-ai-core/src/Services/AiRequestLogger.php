<?php

namespace Haida\FilamentAiCore\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAiCore\DataTransferObjects\AiResult;
use Haida\FilamentAiCore\Models\AiRequest;
use Illuminate\Contracts\Auth\Authenticatable;

class AiRequestLogger
{
    /**
     * @param  array<string, mixed>  $input
     */
    public function record(
        string $module,
        string $actionType,
        array $input,
        AiResult $result,
        ?Authenticatable $actor = null,
        ?int $tenantId = null,
    ): AiRequest {
        $tenantId ??= TenantContext::getTenantId();

        $inputHash = hash('sha256', json_encode($input, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $outputPayload = $result->output_json ?? $result->output_text ?? $result->error ?? '';
        $outputHash = $outputPayload !== '' ? hash('sha256', json_encode($outputPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) : null;

        $status = $result->ok ? 'success' : ($result->error === 'disabled' ? 'disabled' : 'failed');

        return AiRequest::query()->create([
            'tenant_id' => $tenantId,
            'actor_id' => $actor?->getAuthIdentifier(),
            'module' => $module,
            'action_type' => $actionType,
            'input_hash' => $inputHash,
            'output_hash' => $outputHash,
            'status' => $status,
            'latency_ms' => $result->latency_ms,
            'created_at' => now(),
        ]);
    }
}
