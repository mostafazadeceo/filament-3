<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Events\CapabilityRegistered;
use Filamat\IamSuite\Support\RegisteredCapability;
use Illuminate\Contracts\Events\Dispatcher;

class CapabilityRegistry implements CapabilityRegistryInterface
{
    /** @var array<int, RegisteredCapability> */
    protected array $capabilities = [];

    public function __construct(protected Dispatcher $events) {}

    public function register(string $module, array $permissions, array $featureFlags = [], array $quotas = [], array $policies = [], array $navigation = []): RegisteredCapability
    {
        $capability = new RegisteredCapability($module, $permissions, $featureFlags, $quotas, $policies, $navigation);
        $this->capabilities[] = $capability;

        $this->events->dispatch(new CapabilityRegistered($capability));

        return $capability;
    }

    public function all(): array
    {
        return $this->capabilities;
    }
}
