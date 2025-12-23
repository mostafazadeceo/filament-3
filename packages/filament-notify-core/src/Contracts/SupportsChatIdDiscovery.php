<?php

namespace Haida\FilamentNotify\Core\Contracts;

use Haida\FilamentNotify\Core\Support\Sending\DeliveryResult;

interface SupportsChatIdDiscovery
{
    /**
     * @param  array<string, mixed>  $settings
     */
    public function discoverChatIds(array $settings): DeliveryResult;
}
