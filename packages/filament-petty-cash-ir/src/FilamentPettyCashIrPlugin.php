<?php

namespace Haida\FilamentPettyCashIr;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashCategoryResource;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashExpenseResource;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashFundResource;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashReplenishmentResource;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashSettlementResource;

class FilamentPettyCashIrPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'petty-cash-ir';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PettyCashFundResource::class,
            PettyCashCategoryResource::class,
            PettyCashExpenseResource::class,
            PettyCashReplenishmentResource::class,
            PettyCashSettlementResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Plugin boot hook.
    }
}
