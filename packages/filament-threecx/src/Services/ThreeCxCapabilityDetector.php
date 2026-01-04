<?php

namespace Haida\FilamentThreeCx\Services;

use Haida\FilamentThreeCx\Clients\XapiClient;
use Haida\FilamentThreeCx\Contracts\ThreeCxCapabilityDetectorInterface;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;

class ThreeCxCapabilityDetector implements ThreeCxCapabilityDetectorInterface
{
    public function detect(ThreeCxInstance $instance): array
    {
        $client = app(XapiClient::class, ['instance' => $instance]);

        return $client->capabilities();
    }
}
