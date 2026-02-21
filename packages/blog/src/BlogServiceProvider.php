<?php

namespace Haida\Blog;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\Blog\Models\BlogCategory;
use Haida\Blog\Models\BlogPost;
use Haida\Blog\Models\BlogTag;
use Haida\Blog\Policies\BlogCategoryPolicy;
use Haida\Blog\Policies\BlogPostPolicy;
use Haida\Blog\Policies\BlogTagPolicy;
use Haida\Blog\Services\BlogPostService;
use Haida\Blog\Support\BlogCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BlogServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('blog')
            ->hasConfigFile('blog')
            ->hasViews('blog')
            ->hasRoutes('api')
            ->hasRoutes('web')
            ->hasMigrations([
                '2025_12_30_000009_create_blog_tables',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(BlogPostService::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(BlogPost::class, BlogPostPolicy::class);
        Gate::policy(BlogCategory::class, BlogCategoryPolicy::class);
        Gate::policy(BlogTag::class, BlogTagPolicy::class);

        if (interface_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            BlogCapabilities::register($registry);
        }
    }
}
