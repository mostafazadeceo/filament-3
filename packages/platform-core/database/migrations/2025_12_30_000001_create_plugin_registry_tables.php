<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('platform-core.tables', []);
        $registryTable = $tables['plugin_registry'] ?? 'plugin_registry';
        $migrationsTable = $tables['plugin_migrations'] ?? 'plugin_migrations';
        $tenantPluginsTable = $tables['tenant_plugins'] ?? 'tenant_plugins';

        if (! Schema::hasTable($registryTable)) {
            Schema::create($registryTable, function (Blueprint $table) {
                $table->id();
                $table->string('plugin_key')->unique();
                $table->string('name_fa');
                $table->string('description_fa')->nullable();
                $table->string('version');
                $table->string('created_at_jalali', 32);
                $table->string('status')->default(config('platform-core.status.installed', 'installed'));
                $table->timestamp('installed_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable($migrationsTable)) {
            Schema::create($migrationsTable, function (Blueprint $table) use ($registryTable) {
                $table->id();
                $table->string('plugin_key');
                $table->string('version');
                $table->unsignedInteger('migration_batch');
                $table->string('direction', 8)->default('up');
                $table->timestamp('applied_at')->nullable();
                $table->index(['plugin_key', 'version']);
                $table->index(['plugin_key', 'migration_batch']);
                $table
                    ->foreign('plugin_key')
                    ->references('plugin_key')
                    ->on($registryTable)
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable($tenantPluginsTable)) {
            Schema::create($tenantPluginsTable, function (Blueprint $table) use ($registryTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('plugin_key');
                $table->boolean('enabled')->default(false);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->json('limits')->nullable();
                $table->timestamps();
                $table->unique(['tenant_id', 'plugin_key']);
                $table->index(['plugin_key', 'enabled']);
                $table->index(['starts_at', 'ends_at']);
                $table
                    ->foreign('plugin_key')
                    ->references('plugin_key')
                    ->on($registryTable)
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        $tables = config('platform-core.tables', []);
        $registryTable = $tables['plugin_registry'] ?? 'plugin_registry';
        $migrationsTable = $tables['plugin_migrations'] ?? 'plugin_migrations';
        $tenantPluginsTable = $tables['tenant_plugins'] ?? 'tenant_plugins';

        Schema::dropIfExists($tenantPluginsTable);
        Schema::dropIfExists($migrationsTable);
        Schema::dropIfExists($registryTable);
    }
};
