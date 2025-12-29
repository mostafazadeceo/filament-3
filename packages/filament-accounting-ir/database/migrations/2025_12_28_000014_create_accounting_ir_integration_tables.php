<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ir_integration_connectors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('connector_type')->default('rest');
            $table->string('schedule')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'is_active'], 'air_integration_connectors_tenant_company_active_idx');
        });

        Schema::create('accounting_ir_integration_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('integration_connector_id')
                ->constrained('accounting_ir_integration_connectors', 'id', 'air_integration_runs_connector_fk')
                ->cascadeOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->string('status')->default('running');
            $table->json('summary')->nullable();
            $table->timestamps();
        });

        Schema::create('accounting_ir_integration_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('integration_run_id')
                ->constrained('accounting_ir_integration_runs', 'id', 'air_integration_logs_run_fk')
                ->cascadeOnDelete();
            $table->string('level')->default('info');
            $table->text('message');
            $table->json('context')->nullable();
            $table->timestamps();
        });

        Schema::create('accounting_ir_integration_mappings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('integration_connector_id')
                ->constrained('accounting_ir_integration_connectors', 'id', 'air_integration_mappings_connector_fk')
                ->cascadeOnDelete();
            $table->string('entity');
            $table->json('mapping');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ir_integration_mappings');
        Schema::dropIfExists('accounting_ir_integration_logs');
        Schema::dropIfExists('accounting_ir_integration_runs');
        Schema::dropIfExists('accounting_ir_integration_connectors');
    }
};
