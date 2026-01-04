<?php

namespace Haida\FilamentCryptoCore\Tests;

use Filamat\IamSuite\FilamatIamSuiteServiceProvider;
use Haida\FilamentCryptoCore\FilamentCryptoCoreServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            FilamentCryptoCoreServiceProvider::class,
            FilamatIamSuiteServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('app.key', 'base64:3k3n1qA1tG3vDUpX6nTK6oLzIlwE0ZgVf9i1a0c1n3k=');
        $app['config']->set('app.cipher', 'AES-256-CBC');

        $app->singleton('filament', fn () => new class
        {
            public function getTenant()
            {
                return null;
            }

            public function getCurrentPanel()
            {
                return null;
            }
        });
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../../filamat-iam-suite/database/migrations');
    }
}
