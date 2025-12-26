<?php

namespace Haida\FilamentRelograde;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentRelograde\Pages\RelogradeDashboard;
use Haida\FilamentRelograde\Resources\RelogradeAccountResource;
use Haida\FilamentRelograde\Resources\RelogradeAlertResource;
use Haida\FilamentRelograde\Resources\RelogradeApiLogResource;
use Haida\FilamentRelograde\Resources\RelogradeAuditLogResource;
use Haida\FilamentRelograde\Resources\RelogradeBrandResource;
use Haida\FilamentRelograde\Resources\RelogradeConnectionResource;
use Haida\FilamentRelograde\Resources\RelogradeOrderResource;
use Haida\FilamentRelograde\Resources\RelogradeProductResource;
use Haida\FilamentRelograde\Resources\RelogradeWebhookEventResource;
use Haida\FilamentRelograde\Widgets\RelogradeAlertsWidget;
use Haida\FilamentRelograde\Widgets\RelogradeBalanceWidget;
use Haida\FilamentRelograde\Widgets\RelogradeLowBalanceWidget;
use Haida\FilamentRelograde\Widgets\RelogradeOrdersStatusWidget;
use Haida\FilamentRelograde\Widgets\RelogradeStockWidget;
use Haida\FilamentRelograde\Widgets\RelogradeSyncStatusWidget;

class RelogradePlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'relograde';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                RelogradeDashboard::class,
            ])
            ->widgets([
                RelogradeBalanceWidget::class,
                RelogradeSyncStatusWidget::class,
                RelogradeOrdersStatusWidget::class,
                RelogradeLowBalanceWidget::class,
                RelogradeStockWidget::class,
                RelogradeAlertsWidget::class,
            ])
            ->resources([
                RelogradeConnectionResource::class,
                RelogradeBrandResource::class,
                RelogradeProductResource::class,
                RelogradeAccountResource::class,
                RelogradeOrderResource::class,
                RelogradeWebhookEventResource::class,
                RelogradeApiLogResource::class,
                RelogradeAuditLogResource::class,
                RelogradeAlertResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // Plugin boot hook.
    }
}
