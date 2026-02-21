<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('filament-mailops.tables.domains', 'mailops_domains');
        if (! Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (! Schema::hasColumn($tableName, 'dns_health_status')) {
                $table->string('dns_health_status')->default('unknown')->after('dns_snapshot');
            }

            if (! Schema::hasColumn($tableName, 'dns_health_score')) {
                $table->unsignedSmallInteger('dns_health_score')->nullable()->after('dns_health_status');
            }

            if (! Schema::hasColumn($tableName, 'dns_last_checked_at')) {
                $table->timestamp('dns_last_checked_at')->nullable()->after('dns_health_score');
            }

            if (! Schema::hasColumn($tableName, 'dns_issues')) {
                $table->json('dns_issues')->nullable()->after('dns_last_checked_at');
            }
        });

        try {
            Schema::table($tableName, function (Blueprint $table) {
                $table->index(['tenant_id', 'dns_health_status'], 'mailops_domains_tenant_dns_health_idx');
            });
        } catch (Throwable) {
            // Ignore duplicate index creation attempts.
        }
    }

    public function down(): void
    {
        $tableName = config('filament-mailops.tables.domains', 'mailops_domains');
        if (! Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) {
            try {
                $table->dropIndex('mailops_domains_tenant_dns_health_idx');
            } catch (Throwable) {
                // Ignore missing index on rollback.
            }
        });

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            $dropColumns = [];
            foreach (['dns_health_status', 'dns_health_score', 'dns_last_checked_at', 'dns_issues'] as $column) {
                if (Schema::hasColumn($tableName, $column)) {
                    $dropColumns[] = $column;
                }
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
