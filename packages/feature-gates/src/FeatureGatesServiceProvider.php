<?php

namespace Haida\FeatureGates;

use Haida\FeatureGates\Services\FeatureGateService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FeatureGatesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('feature-gates')
            ->hasConfigFile('feature-gates')
            ->hasMigrations([
                '2025_12_30_000002_create_plan_features_table',
                '2025_12_30_000003_create_tenant_feature_overrides_table',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(FeatureGateService::class);
    }
}
