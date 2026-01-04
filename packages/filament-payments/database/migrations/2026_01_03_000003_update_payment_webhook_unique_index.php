<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('filament-payments.tables', []);
        $webhooksTable = $tables['webhook_events'] ?? 'payments_webhook_events';

        if (! Schema::hasTable($webhooksTable)) {
            return;
        }

        $legacyIndex = "{$webhooksTable}_provider_external_id_unique";
        $newIndex = "{$webhooksTable}_tenant_provider_external_id_unique";

        $driver = Schema::getConnection()->getDriverName();
        $indexExists = function (string $indexName) use ($driver, $webhooksTable): bool {
            if ($driver === 'sqlite') {
                $rows = DB::select("PRAGMA index_list('{$webhooksTable}')");
                foreach ($rows as $row) {
                    $name = $row->name ?? $row->index_name ?? null;
                    if ($name === $indexName) {
                        return true;
                    }
                }

                return false;
            }

            if ($driver === 'pgsql') {
                $rows = DB::select('SELECT indexname FROM pg_indexes WHERE tablename = ?', [$webhooksTable]);
                foreach ($rows as $row) {
                    if (($row->indexname ?? null) === $indexName) {
                        return true;
                    }
                }

                return false;
            }

            return DB::select("SHOW INDEX FROM {$webhooksTable} WHERE Key_name = ?", [$indexName]) !== [];
        };

        $legacyExists = $indexExists($legacyIndex);
        if ($legacyExists) {
            Schema::table($webhooksTable, function (Blueprint $table) use ($legacyIndex): void {
                $table->dropUnique($legacyIndex);
            });
        }

        $newExists = $indexExists($newIndex);
        if (! $newExists) {
            Schema::table($webhooksTable, function (Blueprint $table) use ($newIndex): void {
                $table->unique(['tenant_id', 'provider', 'external_id'], $newIndex);
            });
        }
    }

    public function down(): void
    {
        $tables = config('filament-payments.tables', []);
        $webhooksTable = $tables['webhook_events'] ?? 'payments_webhook_events';

        if (! Schema::hasTable($webhooksTable)) {
            return;
        }

        $legacyIndex = "{$webhooksTable}_provider_external_id_unique";
        $newIndex = "{$webhooksTable}_tenant_provider_external_id_unique";

        $driver = Schema::getConnection()->getDriverName();
        $indexExists = function (string $indexName) use ($driver, $webhooksTable): bool {
            if ($driver === 'sqlite') {
                $rows = DB::select("PRAGMA index_list('{$webhooksTable}')");
                foreach ($rows as $row) {
                    $name = $row->name ?? $row->index_name ?? null;
                    if ($name === $indexName) {
                        return true;
                    }
                }

                return false;
            }

            if ($driver === 'pgsql') {
                $rows = DB::select('SELECT indexname FROM pg_indexes WHERE tablename = ?', [$webhooksTable]);
                foreach ($rows as $row) {
                    if (($row->indexname ?? null) === $indexName) {
                        return true;
                    }
                }

                return false;
            }

            return DB::select("SHOW INDEX FROM {$webhooksTable} WHERE Key_name = ?", [$indexName]) !== [];
        };

        $newExists = $indexExists($newIndex);
        if ($newExists) {
            Schema::table($webhooksTable, function (Blueprint $table) use ($newIndex): void {
                $table->dropUnique($newIndex);
            });
        }

        $legacyExists = $indexExists($legacyIndex);
        if (! $legacyExists) {
            Schema::table($webhooksTable, function (Blueprint $table) use ($legacyIndex): void {
                $table->unique(['provider', 'external_id'], $legacyIndex);
            });
        }
    }
};
