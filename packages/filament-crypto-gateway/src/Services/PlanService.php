<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Services;

use Haida\FilamentCryptoCore\Services\FeePolicyEngine;

class PlanService
{
    public function __construct(protected FeePolicyEngine $engine) {}

    public function allowsFeature(?int $tenantId, string $featureKey): bool
    {
        return $this->engine->featureEnabled($featureKey, $tenantId);
    }
}
