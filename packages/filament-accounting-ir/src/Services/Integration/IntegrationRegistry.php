<?php

namespace Vendor\FilamentAccountingIr\Services\Integration;

use Vendor\FilamentAccountingIr\Services\Integration\Contracts\IntegrationConnector;

class IntegrationRegistry
{
    public function resolve(string $type): IntegrationConnector
    {
        $connectors = (array) config('filament-accounting-ir.integration.connectors', []);
        $connectorClass = $connectors[$type] ?? null;

        if (! $connectorClass || ! class_exists($connectorClass)) {
            throw new \RuntimeException("Integration connector not found for type [{$type}].");
        }

        return app($connectorClass);
    }
}
