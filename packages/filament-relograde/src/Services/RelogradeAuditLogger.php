<?php

namespace Haida\FilamentRelograde\Services;

use Haida\FilamentRelograde\Models\RelogradeAuditLog;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Illuminate\Support\Arr;

class RelogradeAuditLogger
{
    public function log(string $action, ?RelogradeConnection $connection = null, array $data = []): void
    {
        $request = app()->runningInConsole() ? null : request();

        RelogradeAuditLog::create([
            'connection_id' => $connection?->getKey(),
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $data['entity_type'] ?? null,
            'entity_id' => $data['entity_id'] ?? null,
            'payload' => Arr::except($data, ['entity_type', 'entity_id']),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
