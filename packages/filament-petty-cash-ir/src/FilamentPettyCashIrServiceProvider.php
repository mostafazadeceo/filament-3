<?php

namespace Haida\FilamentPettyCashIr;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentPettyCashIr\Models\PettyCashCategory;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;
use Haida\FilamentPettyCashIr\Policies\PettyCashCategoryPolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashExpensePolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashFundPolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashReplenishmentPolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashSettlementPolicy;
use Haida\FilamentPettyCashIr\Support\PettyCashCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentPettyCashIrServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-petty-cash-ir')
            ->hasConfigFile('filament-petty-cash-ir')
            ->hasTranslations()
            ->hasRoutes('api')
            ->hasMigrations([
                '2026_01_01_000001_create_petty_cash_core_tables',
                '2026_01_01_000002_create_petty_cash_expense_tables',
                '2026_01_01_000003_create_petty_cash_settlement_tables',
                '2026_01_01_000004_create_petty_cash_audit_tables',
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        Gate::policy(PettyCashFund::class, PettyCashFundPolicy::class);
        Gate::policy(PettyCashCategory::class, PettyCashCategoryPolicy::class);
        Gate::policy(PettyCashExpense::class, PettyCashExpensePolicy::class);
        Gate::policy(PettyCashReplenishment::class, PettyCashReplenishmentPolicy::class);
        Gate::policy(PettyCashSettlement::class, PettyCashSettlementPolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            PettyCashCapabilities::register($registry);
        }

        Gate::define('petty_cash.view', fn () => IamAuthorization::allows('petty_cash.view'));
    }
}
