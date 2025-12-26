<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\AuditLog;
use Filamat\IamSuite\Models\Tenant;

class AuditHashService
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{prev_hash: ?string, hash: string, hash_algo: string}
     */
    public function buildHash(?Tenant $tenant, array $payload): array
    {
        $algo = (string) config('filamat-iam.audit.hash_algo', 'sha256');

        $prevHash = AuditLog::query()
            ->where('tenant_id', $tenant?->getKey())
            ->orderByDesc('id')
            ->value('hash');

        $hash = $this->computeHash($prevHash, $payload, $algo);

        return [
            'prev_hash' => $prevHash,
            'hash' => $hash,
            'hash_algo' => $algo,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function computeHash(?string $prevHash, array $payload, string $algo): string
    {
        $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return hash($algo, ($prevHash ?? '').'|'.($encoded ?: ''));
    }
}
