<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_ai_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_ai_log_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_ai_log_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('actor_id')
                ->nullable()
                ->constrained('users', 'id', 'payroll_ai_log_actor_fk')
                ->nullOnDelete();
            $table->string('report_type');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('provider');
            $table->string('input_hash')->nullable();
            $table->text('response_summary')->nullable();
            $table->json('input_payload')->nullable();
            $table->json('output_payload')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTime('created_at');

            $table->index(['tenant_id', 'company_id', 'report_type'], 'payroll_ai_log_tenant_company_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_ai_logs');
    }
};
