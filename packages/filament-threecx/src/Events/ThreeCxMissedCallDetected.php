<?php

namespace Haida\FilamentThreeCx\Events;

use Haida\FilamentThreeCx\Models\ThreeCxCallLog;

class ThreeCxMissedCallDetected
{
    public const NAME = 'threecx.missed_call_detected';

    public function __construct(public ThreeCxCallLog $callLog) {}
}
