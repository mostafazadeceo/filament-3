<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Contracts;

use Filamat\IamSuite\Support\RegisteredCapability;

interface CapabilityRegistryInterface
{
    /**
     * @param  array<int, string>  $permissions
     * @param  array<string, mixed>  $featureFlags
     * @param  array<string, mixed>  $quotas
     */
    public function register(string $module, array $permissions, array $featureFlags = [], array $quotas = [], array $policies = [], array $navigation = []): RegisteredCapability;

    /**
     * @return array<int, RegisteredCapability>
     */
    public function all(): array;
}
