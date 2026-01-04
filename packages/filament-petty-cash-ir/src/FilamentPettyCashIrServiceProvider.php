<?php

namespace Haida\FilamentPettyCashIr;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentPettyCashIr\Models\PettyCashCashCount;
use Haida\FilamentPettyCashIr\Models\PettyCashCategory;
use Haida\FilamentPettyCashIr\Models\PettyCashControlException;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Models\PettyCashReconciliation;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;
use Haida\FilamentPettyCashIr\Models\PettyCashWorkflowRule;
use Haida\FilamentPettyCashIr\Policies\PettyCashCashCountPolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashCategoryPolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashControlExceptionPolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashExpensePolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashFundPolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashReconciliationPolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashReplenishmentPolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashSettlementPolicy;
use Haida\FilamentPettyCashIr\Policies\PettyCashWorkflowRulePolicy;
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
            ->hasViews()
            ->hasTranslations()
            ->hasRoutes('api')
            ->hasMigrations([
                '2026_01_01_000001_create_petty_cash_core_tables',
                '2026_01_01_000002_create_petty_cash_expense_tables',
                '2026_01_01_000003_create_petty_cash_settlement_tables',
                '2026_01_01_000004_create_petty_cash_audit_tables',
                '2026_01_01_000005_create_petty_cash_action_logs',
                '2026_01_01_000006_add_reversal_fields_to_petty_cash_tables',
                '2026_01_01_000007_create_petty_cash_workflow_rules',
                '2026_01_01_000008_add_workflow_fields_to_petty_cash_tables',
                '2026_01_01_000009_create_petty_cash_control_tables',
                '2026_01_01_000010_add_hash_to_petty_cash_expense_attachments',
                '2026_01_01_000011_create_petty_cash_ai_suggestions',
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        $this->app->bind(
            \Haida\FilamentPettyCashIr\Infrastructure\Accounting\AccountingAdapterInterface::class,
            \Haida\FilamentPettyCashIr\Infrastructure\Accounting\AccountingIrAdapter::class
        );
        $this->app->bind(
            \Haida\FilamentPettyCashIr\Infrastructure\Audit\AuditLoggerInterface::class,
            \Haida\FilamentPettyCashIr\Infrastructure\Audit\PettyCashAuditLogger::class
        );

        Gate::policy(PettyCashFund::class, PettyCashFundPolicy::class);
        Gate::policy(PettyCashCategory::class, PettyCashCategoryPolicy::class);
        Gate::policy(PettyCashExpense::class, PettyCashExpensePolicy::class);
        Gate::policy(PettyCashReplenishment::class, PettyCashReplenishmentPolicy::class);
        Gate::policy(PettyCashSettlement::class, PettyCashSettlementPolicy::class);
        Gate::policy(PettyCashWorkflowRule::class, PettyCashWorkflowRulePolicy::class);
        Gate::policy(PettyCashControlException::class, PettyCashControlExceptionPolicy::class);
        Gate::policy(PettyCashCashCount::class, PettyCashCashCountPolicy::class);
        Gate::policy(PettyCashReconciliation::class, PettyCashReconciliationPolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            PettyCashCapabilities::register($registry);
        }

        Gate::define('petty_cash.view', fn () => IamAuthorization::allows('petty_cash.view'));
    }
}
