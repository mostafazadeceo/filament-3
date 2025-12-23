<?php

namespace Haida\FilamentNotify\Core;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentNotify\Core\Resources\DeliveryLogResource;
use Haida\FilamentNotify\Core\Resources\NotificationRuleResource;
use Haida\FilamentNotify\Core\Resources\TemplateResource;
use Haida\FilamentNotify\Core\Resources\TriggerResource;
use Haida\FilamentNotify\Core\Pages\ChannelSettingsPage;

class FilamentNotifyPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-notify';
    }

    public function register(Panel $panel): void
    {
        app(FilamentNotifyManager::class)->registerPanel($panel);

        $pages = [ChannelSettingsPage::class];
        if (class_exists(\Haida\FilamentNotify\WebPush\Pages\PushSubscriptionsPage::class)) {
            $pages[] = \Haida\FilamentNotify\WebPush\Pages\PushSubscriptionsPage::class;
        }

        $panel
            ->resources([
                TriggerResource::class,
                TemplateResource::class,
                NotificationRuleResource::class,
                DeliveryLogResource::class,
            ])
            ->pages($pages);
    }

    public function boot(Panel $panel): void
    {
        // Trigger discovery is handled globally via events.
    }
}
