<?php

namespace Haida\ThemeEngine;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ThemeEngineServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('theme-engine')
            ->hasConfigFile('theme-engine')
            ->hasViews('theme-engine');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ThemeRegistry::class, function ($app) {
            $definitions = $app['config']->get('theme-engine.themes', []);

            return new ThemeRegistry(is_array($definitions) ? $definitions : []);
        });
    }

    public function packageBooted(): void
    {
        $this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/theme-engine'),
        ], 'theme-engine-assets');
    }
}
