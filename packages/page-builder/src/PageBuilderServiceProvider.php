<?php

namespace Haida\PageBuilder;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\PageBuilder\Models\PageTemplate;
use Haida\PageBuilder\Policies\PageTemplatePolicy;
use Haida\PageBuilder\Services\HtmlSanitizer;
use Haida\PageBuilder\Services\PageBuilderService;
use Haida\PageBuilder\Support\PageBuilderCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PageBuilderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('page-builder')
            ->hasConfigFile('page-builder')
            ->hasMigrations([
                '2025_12_30_000007_create_page_builder_tables',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(HtmlSanitizer::class, function ($app) {
            $allowedTags = (array) $app['config']->get('page-builder.sanitize.allowed_tags', []);
            $allowedAttributes = (array) $app['config']->get('page-builder.sanitize.allowed_attributes', []);

            return new HtmlSanitizer($allowedTags, $allowedAttributes);
        });

        $this->app->singleton(PageBuilderService::class, function ($app) {
            return new PageBuilderService($app->make(HtmlSanitizer::class));
        });
    }

    public function packageBooted(): void
    {
        Gate::policy(PageTemplate::class, PageTemplatePolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            PageBuilderCapabilities::register($registry);
        }
    }
}
