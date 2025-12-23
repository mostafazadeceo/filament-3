<?php

namespace Haida\FilamentNotify\Core\Support\Context;

use Haida\FilamentNotify\Core\Models\NotificationRule;
use Haida\FilamentNotify\Core\Models\Template;

class DeliveryContext
{
    public function __construct(
        public string $panelId,
        public string $triggerKey,
        public NotificationRule $rule,
        public string $channelKey,
        public array $channelSettings,
        public array $recipient,
        public array $context,
        public ?Template $template,
    ) {}
}
