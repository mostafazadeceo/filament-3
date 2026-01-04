<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Support;

final class N8nEventCatalog
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function events(): array
    {
        $catalog = (string) config('filamat-iam.automation.event_catalog', 'n8n_event_catalog');

        return (array) config($catalog.'.events', []);
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::events() as $key => $meta) {
            $options[$key] = $meta['label_fa'] ?? $key;
        }

        return $options;
    }

    /**
     * @return array<int, string>
     */
    public static function defaultSubscriptions(): array
    {
        $catalog = (string) config('filamat-iam.automation.event_catalog', 'n8n_event_catalog');

        return (array) config($catalog.'.default_subscriptions.enabled_by_default', []);
    }
}
