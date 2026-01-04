<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('providers-core.tables', []);
        $jobLogsTable = $tables['job_logs'] ?? 'providers_core_job_logs';

        if (! Schema::hasTable($jobLogsTable)) {
            Schema::create($jobLogsTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
                $table->string('provider_key');
                $table->string('job_type');
                $table->string('status')->default('pending');
                $table->unsignedBigInteger('connection_id')->nullable();
                $table->unsignedInteger('attempts')->default(0);
                $table->json('payload')->nullable();
                $table->json('result')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('finished_at')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'provider_key', 'status']);
                $table->index(['provider_key', 'job_type']);
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        $tables = config('providers-core.tables', []);
        $jobLogsTable = $tables['job_logs'] ?? 'providers_core_job_logs';

        Schema::dropIfExists($jobLogsTable);
    }
};
