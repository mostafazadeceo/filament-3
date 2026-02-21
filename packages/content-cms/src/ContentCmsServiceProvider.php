<?php

namespace Haida\ContentCms;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\ContentCms\Models\CmsPage;
use Haida\ContentCms\Policies\CmsPagePolicy;
use Haida\ContentCms\Services\CmsPageService;
use Haida\ContentCms\Support\ContentCmsCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ContentCmsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('content-cms')
            ->hasConfigFile('content-cms')
            ->hasViews('content-cms')
            ->hasRoutes('api')
            ->hasRoutes('web')
            ->hasMigrations([
                '2025_12_30_000008_create_content_cms_tables',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(CmsPageService::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(CmsPage::class, CmsPagePolicy::class);

        if (interface_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            ContentCmsCapabilities::register($registry);
        }
    }
}
