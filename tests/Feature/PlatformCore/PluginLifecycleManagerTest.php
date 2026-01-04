<?php

namespace Tests\Feature\PlatformCore;

use Filamat\IamSuite\Models\Tenant;
use Haida\PlatformCore\Models\PluginMigration;
use Haida\PlatformCore\Services\PluginLifecycleManager;
use Haida\PlatformCore\Support\PluginManifest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class PluginLifecycleManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_installs_and_enables_plugin(): void
    {
        $manager = app(PluginLifecycleManager::class);
        $manifest = PluginManifest::fromArray([
            'name_fa' => 'Site Builder Core',
            'description_fa' => 'Lifecycle registry for platform plugins',
            'version' => '1.0.0',
            'created_at_jalali' => '1404/10/09',
            'meta' => ['source' => 'tests'],
        ]);

        $registry = $manager->install('site-builder-core', $manifest);

        $this->assertDatabaseHas($this->registryTable(), [
            'plugin_key' => 'site-builder-core',
            'version' => '1.0.0',
            'status' => 'installed',
        ]);

        $tenant = Tenant::query()->create([
            'name' => 'Tenant One',
            'slug' => 'tenant-one',
            'status' => 'active',
        ]);

        $tenantPlugin = $manager->enable('site-builder-core', $tenant->id);

        $this->assertTrue($tenantPlugin->enabled);
        $this->assertDatabaseHas($this->tenantPluginsTable(), [
            'tenant_id' => $tenant->id,
            'plugin_key' => 'site-builder-core',
            'enabled' => 1,
        ]);
    }

    public function test_upgrade_records_migrations(): void
    {
        $manager = app(PluginLifecycleManager::class);
        $manifest = PluginManifest::fromArray([
            'name_fa' => 'Site Builder Core',
            'description_fa' => 'Lifecycle registry for platform plugins',
            'version' => '1.0.0',
            'created_at_jalali' => '1404/10/09',
        ]);

        $manager->install('site-builder-core', $manifest);
        $user = User::query()->create([
            'name' => 'Tester',
            'email' => 'tester@example.com',
            'password' => 'secret-password',
        ]);

        $this->actingAs($user);
        app()->instance('correlation_id', 'corr-test-001');

        $manager->upgrade('site-builder-core', '1.0.0', '1.1.0');

        $this->assertDatabaseHas($this->registryTable(), [
            'plugin_key' => 'site-builder-core',
            'version' => '1.1.0',
        ]);

        $this->assertDatabaseHas($this->migrationsTable(), [
            'plugin_key' => 'site-builder-core',
            'version' => '1.1.0',
            'direction' => 'up',
            'migration_batch' => 1,
            'triggered_by_user_id' => $user->id,
            'correlation_id' => 'corr-test-001',
        ]);

        $manager->rollback('site-builder-core', '1.1.0', '1.0.0');

        $this->assertDatabaseHas($this->migrationsTable(), [
            'plugin_key' => 'site-builder-core',
            'version' => '1.0.0',
            'direction' => 'down',
            'migration_batch' => 2,
        ]);
    }

    private function registryTable(): string
    {
        return config('platform-core.tables.plugin_registry', 'plugin_registry');
    }

    private function migrationsTable(): string
    {
        return config('platform-core.tables.plugin_migrations', 'plugin_migrations');
    }

    private function tenantPluginsTable(): string
    {
        return config('platform-core.tables.tenant_plugins', 'tenant_plugins');
    }
}
