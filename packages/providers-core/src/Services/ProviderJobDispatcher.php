<?php

declare(strict_types=1);

namespace Haida\ProvidersCore\Services;

use Haida\ProvidersCore\DataTransferObjects\ProviderContext;
use Haida\ProvidersCore\Jobs\ProviderActionJob;
use Haida\ProvidersCore\Models\ProviderJobLog;
use Haida\ProvidersCore\Support\ProviderAction;

class ProviderJobDispatcher
{
    public function dispatch(
        ProviderAction $action,
        ProviderContext $context,
        string $providerKey,
        array $payload = [],
    ): ProviderJobLog {
        $log = ProviderJobLog::query()->create([
            'tenant_id' => $context->tenantId,
            'provider_key' => $providerKey,
            'job_type' => $action->value,
            'status' => 'pending',
            'connection_id' => $context->connectionId,
            'payload' => config('providers-core.logging.store_payloads', true) ? $payload : null,
        ]);

        ProviderActionJob::dispatch(
            $context->tenantId,
            $providerKey,
            $action,
            $payload,
            $log->getKey(),
            $context->connectionId,
            $context->sandbox
        )->onQueue((string) config('providers-core.queue', 'providers'));

        return $log;
    }

    public function dispatchSync(
        ProviderAction $action,
        ProviderContext $context,
        string $providerKey,
        array $payload = [],
    ): ProviderJobLog {
        $log = ProviderJobLog::query()->create([
            'tenant_id' => $context->tenantId,
            'provider_key' => $providerKey,
            'job_type' => $action->value,
            'status' => 'pending',
            'connection_id' => $context->connectionId,
            'payload' => config('providers-core.logging.store_payloads', true) ? $payload : null,
        ]);

        ProviderActionJob::dispatchSync(
            $context->tenantId,
            $providerKey,
            $action,
            $payload,
            $log->getKey(),
            $context->connectionId,
            $context->sandbox
        );

        return $log;
    }
}
