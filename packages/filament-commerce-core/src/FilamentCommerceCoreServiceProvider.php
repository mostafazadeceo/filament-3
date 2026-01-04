<?php

namespace Haida\FilamentCommerceCore;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Observers\AuditableObserver;
use Haida\FilamentCommerceCore\Console\Commands\RunCommerceComplianceDigest;
use Haida\FilamentCommerceCore\Models\CommerceBrand;
use Haida\FilamentCommerceCore\Models\CommerceCategory;
use Haida\FilamentCommerceCore\Models\CommerceComplianceDigest;
use Haida\FilamentCommerceCore\Models\CommerceCustomer;
use Haida\FilamentCommerceCore\Models\CommerceException;
use Haida\FilamentCommerceCore\Models\CommerceFraudRule;
use Haida\FilamentCommerceCore\Models\CommerceInventoryItem;
use Haida\FilamentCommerceCore\Models\CommercePrice;
use Haida\FilamentCommerceCore\Models\CommercePriceList;
use Haida\FilamentCommerceCore\Models\CommerceProduct;
use Haida\FilamentCommerceCore\Models\CommerceStockMove;
use Haida\FilamentCommerceCore\Models\CommerceVariant;
use Haida\FilamentCommerceCore\Policies\CommerceBrandPolicy;
use Haida\FilamentCommerceCore\Policies\CommerceCategoryPolicy;
use Haida\FilamentCommerceCore\Policies\CommerceComplianceDigestPolicy;
use Haida\FilamentCommerceCore\Policies\CommerceCustomerPolicy;
use Haida\FilamentCommerceCore\Policies\CommerceExceptionPolicy;
use Haida\FilamentCommerceCore\Policies\CommerceFraudRulePolicy;
use Haida\FilamentCommerceCore\Policies\CommerceInventoryItemPolicy;
use Haida\FilamentCommerceCore\Policies\CommercePriceListPolicy;
use Haida\FilamentCommerceCore\Policies\CommercePricePolicy;
use Haida\FilamentCommerceCore\Policies\CommerceProductPolicy;
use Haida\FilamentCommerceCore\Policies\CommerceStockMovePolicy;
use Haida\FilamentCommerceCore\Policies\CommerceVariantPolicy;
use Haida\FilamentCommerceCore\Services\CommerceComplianceService;
use Haida\FilamentCommerceCore\Support\CommerceCoreCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentCommerceCoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-commerce-core')
            ->hasConfigFile('filament-commerce-core')
            ->hasRoutes('api')
            ->hasTranslations()
            ->hasCommands([
                RunCommerceComplianceDigest::class,
            ])
            ->hasMigrations([
                '2026_01_02_000001_create_commerce_core_tables',
                '2026_01_03_000007_create_commerce_compliance_tables',
                '2026_01_03_000008_create_commerce_compliance_digest_tables',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(CommerceComplianceService::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(CommerceProduct::class, CommerceProductPolicy::class);
        Gate::policy(CommerceCategory::class, CommerceCategoryPolicy::class);
        Gate::policy(CommerceBrand::class, CommerceBrandPolicy::class);
        Gate::policy(CommerceVariant::class, CommerceVariantPolicy::class);
        Gate::policy(CommercePriceList::class, CommercePriceListPolicy::class);
        Gate::policy(CommercePrice::class, CommercePricePolicy::class);
        Gate::policy(CommerceInventoryItem::class, CommerceInventoryItemPolicy::class);
        Gate::policy(CommerceStockMove::class, CommerceStockMovePolicy::class);
        Gate::policy(CommerceCustomer::class, CommerceCustomerPolicy::class);
        Gate::policy(CommerceException::class, CommerceExceptionPolicy::class);
        Gate::policy(CommerceFraudRule::class, CommerceFraudRulePolicy::class);
        Gate::policy(CommerceComplianceDigest::class, CommerceComplianceDigestPolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            CommerceCoreCapabilities::register($registry);
        }

        if (config('filamat-iam.audit.enabled', true)) {
            CommerceException::observe(AuditableObserver::class);
            CommerceFraudRule::observe(AuditableObserver::class);
            CommerceComplianceDigest::observe(AuditableObserver::class);
        }
    }
}
