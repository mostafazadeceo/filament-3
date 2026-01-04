<?php

namespace Haida\FilamentPettyCashIr;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentPettyCashIr\Filament\Pages\PettyCashAiAuditPage;
use Haida\FilamentPettyCashIr\Filament\Pages\PettyCashAiReportPage;
use Haida\FilamentPettyCashIr\Filament\Pages\PettyCashDashboard;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashCashCountResource;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashCategoryResource;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashControlExceptionResource;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashExpenseResource;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashFundResource;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashReconciliationResource;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashReplenishmentResource;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashSettlementResource;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashWorkflowRuleResource;
use Haida\FilamentPettyCashIr\Filament\Widgets\PettyCashOverviewWidget;

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
            PettyCashWorkflowRuleResource::class,
            PettyCashControlExceptionResource::class,
            PettyCashCashCountResource::class,
            PettyCashReconciliationResource::class,
        ])
            ->pages([
                PettyCashDashboard::class,
                PettyCashAiAuditPage::class,
                PettyCashAiReportPage::class,
            ])
            ->widgets([
                PettyCashOverviewWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // Plugin boot hook.
    }
}
