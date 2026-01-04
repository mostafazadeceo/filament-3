<?php

namespace Haida\PaymentsOrchestrator\Services;

use Haida\PaymentsOrchestrator\Contracts\GatewayAdapterInterface;
use InvalidArgumentException;

class GatewayRegistry
{
    public function get(string $providerKey): GatewayAdapterInterface
    {
        $map = (array) config('payments-orchestrator.adapters', []);
        $class = $map[$providerKey] ?? null;

        if (! $class || ! class_exists($class)) {
            throw new InvalidArgumentException('Payment provider adapter is not registered.');
        }

        $adapter = app($class);
        if (! $adapter instanceof GatewayAdapterInterface) {
            throw new InvalidArgumentException('Payment adapter must implement GatewayAdapterInterface.');
        }

        return $adapter;
    }
}
