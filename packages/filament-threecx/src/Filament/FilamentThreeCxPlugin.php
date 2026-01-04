<?php

namespace Haida\FilamentThreeCx\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentThreeCx\Filament\Pages\ThreeCxApiExplorerPage;
use Haida\FilamentThreeCx\Filament\Resources\ThreeCxCallLogResource;
use Haida\FilamentThreeCx\Filament\Resources\ThreeCxContactResource;
use Haida\FilamentThreeCx\Filament\Resources\ThreeCxInstanceResource;
use Haida\FilamentThreeCx\Filament\Widgets\ThreeCxCallOverviewWidget;
use Haida\FilamentThreeCx\Filament\Widgets\ThreeCxSyncStatusWidget;

class FilamentThreeCxPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-threecx';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                ThreeCxInstanceResource::class,
                ThreeCxCallLogResource::class,
                ThreeCxContactResource::class,
            ])
            ->pages([
                ThreeCxApiExplorerPage::class,
            ])
            ->widgets([
                ThreeCxCallOverviewWidget::class,
                ThreeCxSyncStatusWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op for now.
    }
}
