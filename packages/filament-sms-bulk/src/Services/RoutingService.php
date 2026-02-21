<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Services;

use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Models\SmsBulkRoutingPolicy;

class RoutingService
{
    /**
     * @return array{primary: ?SmsBulkProviderConnection, fallback: ?SmsBulkProviderConnection}
     */
    public function resolve(int $tenantId, ?int $preferredConnectionId = null): array
    {
        if ($preferredConnectionId) {
            $primary = SmsBulkProviderConnection::query()
                ->where('tenant_id', $tenantId)
                ->whereKey($preferredConnectionId)
                ->first();

            return [
                'primary' => $primary,
                'fallback' => null,
            ];
        }

        $routing = SmsBulkRoutingPolicy::query()->where('tenant_id', $tenantId)->first();

        return [
            'primary' => $routing?->primaryConnection,
            'fallback' => $routing?->fallbackConnection,
        ];
    }
}
