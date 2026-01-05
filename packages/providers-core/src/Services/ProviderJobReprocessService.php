<?php

declare(strict_types=1);

namespace Haida\ProvidersCore\Services;

use Haida\ProvidersCore\DataTransferObjects\ProviderContext;
use Haida\ProvidersCore\Models\ProviderJobLog;
use Haida\ProvidersCore\Support\ProviderAction;
use InvalidArgumentException;

class ProviderJobReprocessService
{
    public function __construct(private readonly ProviderJobDispatcher $dispatcher) {}

    public function reprocess(ProviderJobLog $log): ProviderJobLog
    {
        $action = ProviderAction::tryFrom($log->job_type);
        if (! $action) {
            throw new InvalidArgumentException('نوع عملیات ناشناخته است.');
        }

        $context = new ProviderContext(
            $log->tenant_id,
            $log->connection_id,
            false
        );

        return $this->dispatcher->dispatch(
            $action,
            $context,
            $log->provider_key,
            is_array($log->payload) ? $log->payload : []
        );
    }
}
