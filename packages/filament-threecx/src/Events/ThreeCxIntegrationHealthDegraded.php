<?php

namespace Haida\FilamentThreeCx\Events;

use Haida\FilamentThreeCx\Models\ThreeCxInstance;

class ThreeCxIntegrationHealthDegraded
{
    public const NAME = 'threecx.health_degraded';

    public function __construct(public ThreeCxInstance $instance, public string $message) {}
}
