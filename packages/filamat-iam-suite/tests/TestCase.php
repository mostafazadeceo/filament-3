<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Tests;

use Filamat\IamSuite\FilamatIamSuiteServiceProvider;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\PermissionServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        TenantContext::setTenant(null);
        TenantContext::bypass(false);
        app(PermissionRegistrar::class)->initializeCache();
    }

    protected function getPackageProviders($app): array
    {
        return [
            FilamatIamSuiteServiceProvider::class,
            PermissionServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('auth.providers.users.model', \Filamat\IamSuite\Tests\Fixtures\User::class);
        $app['config']->set('permission.models.permission', Permission::class);
        $app['config']->set('permission.models.role', Role::class);
        $app['config']->set('permission.table_names.roles', 'roles');
        $app['config']->set('permission.table_names.permissions', 'permissions');
        $app['config']->set('permission.table_names.model_has_roles', 'model_has_roles');
        $app['config']->set('permission.table_names.model_has_permissions', 'model_has_permissions');
        $app['config']->set('permission.table_names.role_has_permissions', 'role_has_permissions');
        $app['config']->set('permission.cache.key', 'spatie.permission.cache');
        $app['config']->set('permission.cache.store', 'array');
        $app['config']->set('permission.teams', true);
        $app['config']->set('permission.column_names.model_morph_key', 'model_id');
        $app['config']->set('permission.column_names.role_pivot_key', 'role_id');
        $app['config']->set('permission.column_names.permission_pivot_key', 'permission_id');
        $app['config']->set('permission.column_names.team_foreign_key', 'tenant_id');
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
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
