<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_ir_payroll_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->date('run_date')->nullable();
            $table->string('run_type')->default('monthly');
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('posted_at')->nullable();
            $table->dateTime('locked_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'status'], 'pir_payroll_run_tenant_company_status_idx');
            $table->index(['company_id', 'period_start', 'period_end'], 'pir_payroll_run_period_idx');
            $table->index(['status', 'run_date'], 'pir_payroll_run_status_date_idx');
        });

        Schema::create('payroll_ir_payroll_slips', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->foreignId('payroll_run_id')->constrained('payroll_ir_payroll_runs')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('payroll_ir_employees')->cascadeOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained('payroll_ir_contracts')->nullOnDelete();
            $table->string('slip_type')->default('official');
            $table->string('status')->default('draft');
            $table->decimal('gross_amount', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->decimal('taxable_amount', 18, 2)->default(0);
            $table->decimal('insurance_amount_employee', 18, 2)->default(0);
            $table->decimal('insurance_amount_employer', 18, 2)->default(0);
            $table->decimal('total_allowances', 18, 2)->default(0);
            $table->decimal('total_deductions', 18, 2)->default(0);
            $table->dateTime('issued_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['payroll_run_id', 'employee_id', 'slip_type'], 'pir_payroll_slip_unique');
            $table->index(['employee_id', 'slip_type', 'status'], 'pir_payroll_slip_employee_idx');
        });

        Schema::create('payroll_ir_payroll_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('payroll_slip_id')->constrained('payroll_ir_payroll_slips')->cascadeOnDelete();
            $table->string('item_type')->default('allowance');
            $table->string('code')->nullable();
            $table->string('title');
            $table->decimal('amount', 18, 2)->default(0);
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_insurable')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['payroll_slip_id'], 'pir_payroll_item_slip_idx');
            $table->index(['code'], 'pir_payroll_item_code_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_ir_payroll_items');
        Schema::dropIfExists('payroll_ir_payroll_slips');
        Schema::dropIfExists('payroll_ir_payroll_runs');
    }
};
