<?php

namespace Haida\FilamentPettyCashIr\Infrastructure\Idempotency;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class IdempotencyService
{
    public function run(
        string $action,
        Model $subject,
        ?string $idempotencyKey,
        ?int $actorId,
        callable $callback,
        array $metadata = []
    ): mixed {
        if (! config('filament-petty-cash-ir.idempotency.enabled', true)) {
            return $callback();
        }

        if (! $idempotencyKey) {
            return $callback();
        }

        $existing = PettyCashActionLog::query()
            ->where('tenant_id', $subject->tenant_id ?? null)
            ->where('company_id', $subject->company_id ?? null)
            ->where('action', $action)
            ->where('subject_type', $subject::class)
            ->where('subject_id', $subject->getKey())
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        if ($existing) {
            return $subject->refresh();
        }

        $log = null;
        try {
            $log = PettyCashActionLog::query()->create([
                'tenant_id' => $subject->tenant_id ?? null,
                'company_id' => $subject->company_id ?? null,
                'action' => $action,
                'subject_type' => $subject::class,
                'subject_id' => $subject->getKey(),
                'idempotency_key' => $idempotencyKey,
                'status' => 'started',
                'actor_id' => $actorId,
                'metadata' => $metadata,
            ]);
        } catch (QueryException $exception) {
            return $subject->refresh();
        }

        try {
            $result = $callback();
            $log?->update([
                'status' => 'completed',
            ]);

            return $result;
        } catch (\Throwable $throwable) {
            $log?->update([
                'status' => 'failed',
                'metadata' => array_merge($metadata, ['error' => $throwable->getMessage()]),
            ]);

            throw $throwable;
        }
    }
}
