<?php

namespace Haida\FilamentThreeCx\Contracts;

use Haida\FilamentThreeCx\Models\ThreeCxInstance;

interface ThreeCxCapabilityDetectorInterface
{
    /**
     * @return array<string, mixed>
     */
    public function detect(ThreeCxInstance $instance): array;
}
