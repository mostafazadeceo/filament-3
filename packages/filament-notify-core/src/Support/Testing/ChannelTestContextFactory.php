<?php

namespace Haida\FilamentNotify\Core\Support\Testing;

use Haida\FilamentNotify\Core\Models\NotificationRule;
use Haida\FilamentNotify\Core\Support\Context\DeliveryContext;

class ChannelTestContextFactory
{
    /**
     * @param  array<string, mixed>  $channelSettings
     * @param  array<string, mixed>  $recipient
     * @param  array<string, mixed>  $context
     */
    public static function make(
        string $panelId,
        string $channelKey,
        array $channelSettings,
        array $recipient,
        array $context,
    ): DeliveryContext {
        $rule = new NotificationRule;
        $rule->id = 0;
        $rule->panel_id = $panelId;
        $rule->name = 'test';
        $rule->enabled = true;

        return new DeliveryContext(
            panelId: $panelId,
            triggerKey: 'test.'.$channelKey,
            rule: $rule,
            channelKey: $channelKey,
            channelSettings: $channelSettings,
            recipient: $recipient,
            context: $context,
            template: null,
        );
    }
}
