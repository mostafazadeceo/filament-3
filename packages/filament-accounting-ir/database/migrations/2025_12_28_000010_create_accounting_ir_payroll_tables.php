<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ir_employees', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->string('employee_no')->nullable();
            $table->string('name');
            $table->string('national_id')->nullable();
            $table->date('hire_date')->nullable();
            $table->string('status')->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id', 'status'], 'air_employees_tenant_company_status_idx');
        });

        Schema::create('accounting_ir_payroll_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('fiscal_period_id')->nullable()->constrained('accounting_ir_fiscal_periods')->nullOnDelete();
            $table->date('run_date')->nullable();
            $table->string('status')->default('draft');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'status'], 'air_payroll_runs_tenant_company_status_idx');
        });

        Schema::create('accounting_ir_payroll_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained('accounting_ir_payroll_runs')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('accounting_ir_employees')->cascadeOnDelete();
            $table->decimal('gross', 18, 2)->default(0);
            $table->decimal('net', 18, 2)->default(0);
            $table->decimal('tax', 18, 2)->default(0);
            $table->decimal('insurance', 18, 2)->default(0);
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index(['payroll_run_id', 'employee_id'], 'air_payroll_items_run_employee_idx');
        });

        Schema::create('accounting_ir_payroll_slips', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained('accounting_ir_payroll_runs')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('accounting_ir_employees')->cascadeOnDelete();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['payroll_run_id', 'employee_id'], 'air_payroll_slips_run_employee_idx');
        });

        Schema::create('accounting_ir_payroll_tables', function (Blueprint $table): void {
            $table->id();
            $table->string('table_type');
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->json('payload');
            $table->timestamps();

            $table->index(['table_type', 'effective_from'], 'air_payroll_tables_type_effective_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ir_payroll_tables');
        Schema::dropIfExists('accounting_ir_payroll_slips');
        Schema::dropIfExists('accounting_ir_payroll_items');
        Schema::dropIfExists('accounting_ir_payroll_runs');
        Schema::dropIfExists('accounting_ir_employees');
    }
};
