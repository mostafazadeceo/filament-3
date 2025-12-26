<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Support;

class RegisteredCapability
{
    /** @param array<int, string> $permissions */
    public function __construct(
        public readonly string $module,
        public readonly array $permissions,
        public readonly array $featureFlags = [],
        public readonly array $quotas = [],
        public readonly array $policies = [],
        public readonly array $navigation = [],
    ) {}
}
