<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events;

use Filamat\IamSuite\Support\RegisteredCapability;

class CapabilityRegistered
{
    public function __construct(public readonly RegisteredCapability $capability) {}
}
