<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ir_projects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('status')->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'status'], 'air_projects_tenant_company_status_idx');
        });

        Schema::create('accounting_ir_contracts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('accounting_ir_projects')->nullOnDelete();
            $table->foreignId('party_id')->nullable()->constrained('accounting_ir_parties')->nullOnDelete();
            $table->string('contract_no')->nullable();
            $table->decimal('amount', 18, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'status'], 'air_contracts_tenant_company_status_idx');
        });

        Schema::create('accounting_ir_contract_statements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contract_id')->constrained('accounting_ir_contracts')->cascadeOnDelete();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->decimal('gross_amount', 18, 2)->default(0);
            $table->decimal('deductions', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->string('status')->default('draft');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['contract_id', 'status'], 'air_contract_statements_contract_status_idx');
        });

        Schema::create('accounting_ir_retentions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contract_id')->constrained('accounting_ir_contracts')->cascadeOnDelete();
            $table->decimal('amount', 18, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('status')->default('held');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['contract_id', 'status'], 'air_retentions_contract_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ir_retentions');
        Schema::dropIfExists('accounting_ir_contract_statements');
        Schema::dropIfExists('accounting_ir_contracts');
        Schema::dropIfExists('accounting_ir_projects');
    }
};
