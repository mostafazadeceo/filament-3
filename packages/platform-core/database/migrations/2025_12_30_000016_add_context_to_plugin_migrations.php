<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('platform-core.tables', []);
        $migrationsTable = $tables['plugin_migrations'] ?? 'plugin_migrations';

        if (! Schema::hasTable($migrationsTable)) {
            return;
        }

        Schema::table($migrationsTable, function (Blueprint $table) use ($migrationsTable) {
            if (! Schema::hasColumn($migrationsTable, 'correlation_id')) {
                $table->string('correlation_id', 64)->nullable()->index();
            }

            if (! Schema::hasColumn($migrationsTable, 'triggered_by_user_id')) {
                $table->foreignId('triggered_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        $tables = config('platform-core.tables', []);
        $migrationsTable = $tables['plugin_migrations'] ?? 'plugin_migrations';

        if (! Schema::hasTable($migrationsTable)) {
            return;
        }

        Schema::table($migrationsTable, function (Blueprint $table) use ($migrationsTable) {
            if (Schema::hasColumn($migrationsTable, 'triggered_by_user_id')) {
                $table->dropConstrainedForeignId('triggered_by_user_id');
            }

            if (Schema::hasColumn($migrationsTable, 'correlation_id')) {
                $table->dropColumn('correlation_id');
            }
        });
    }
};
