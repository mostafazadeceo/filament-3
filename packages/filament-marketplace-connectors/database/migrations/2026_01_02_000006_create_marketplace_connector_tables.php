<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('filament-marketplace-connectors.tables', []);
        $connectorsTable = $tables['connectors'] ?? 'mkt_connectors';
        $tokensTable = $tables['tokens'] ?? 'mkt_tokens';
        $syncJobsTable = $tables['sync_jobs'] ?? 'mkt_sync_jobs';
        $syncLogsTable = $tables['sync_logs'] ?? 'mkt_sync_logs';
        $rateLimitsTable = $tables['rate_limits'] ?? 'mkt_rate_limits';

        if (! Schema::hasTable($connectorsTable)) {
            Schema::create($connectorsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('provider_key');
                $table->string('name')->nullable();
                $table->string('status')->default('inactive');
                $table->json('config')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'provider_key']);
                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($tokensTable)) {
            Schema::create($tokensTable, function (Blueprint $table) use ($connectorsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('connector_id')->constrained($connectorsTable)->cascadeOnDelete();
                $table->string('access_token')->nullable();
                $table->string('refresh_token')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->string('scopes')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'connector_id']);
            });
        }

        if (! Schema::hasTable($syncJobsTable)) {
            Schema::create($syncJobsTable, function (Blueprint $table) use ($connectorsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('connector_id')->constrained($connectorsTable)->cascadeOnDelete();
                $table->string('job_type');
                $table->string('status')->default('pending');
                $table->timestamp('last_run_at')->nullable();
                $table->string('error')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'connector_id']);
                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($syncLogsTable)) {
            Schema::create($syncLogsTable, function (Blueprint $table) use ($connectorsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('connector_id')->constrained($connectorsTable)->cascadeOnDelete();
                $table->string('job_type');
                $table->string('status')->default('ok');
                $table->text('message')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'connector_id']);
            });
        }

        if (! Schema::hasTable($rateLimitsTable)) {
            Schema::create($rateLimitsTable, function (Blueprint $table) use ($connectorsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('connector_id')->constrained($connectorsTable)->cascadeOnDelete();
                $table->string('bucket');
                $table->unsignedInteger('limit')->default(0);
                $table->unsignedInteger('remaining')->default(0);
                $table->timestamp('reset_at')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'connector_id', 'bucket']);
            });
        }
    }

    public function down(): void
    {
        $tables = config('filament-marketplace-connectors.tables', []);
        $rateLimitsTable = $tables['rate_limits'] ?? 'mkt_rate_limits';
        $syncLogsTable = $tables['sync_logs'] ?? 'mkt_sync_logs';
        $syncJobsTable = $tables['sync_jobs'] ?? 'mkt_sync_jobs';
        $tokensTable = $tables['tokens'] ?? 'mkt_tokens';
        $connectorsTable = $tables['connectors'] ?? 'mkt_connectors';

        Schema::dropIfExists($rateLimitsTable);
        Schema::dropIfExists($syncLogsTable);
        Schema::dropIfExists($syncJobsTable);
        Schema::dropIfExists($tokensTable);
        Schema::dropIfExists($connectorsTable);
    }
};
