<?php

namespace Haida\FeatureGates\Support;

class FeatureGateDecision
{
    public function __construct(
        public readonly bool $allowed,
        public readonly string $source,
        public readonly ?string $detail = null,
        public readonly ?array $limits = null,
    ) {
    }
}
