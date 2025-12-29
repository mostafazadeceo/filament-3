<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_ir_contracts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->foreignId('employee_id')->constrained('payroll_ir_employees')->cascadeOnDelete();
            $table->string('contract_type')->default('official');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->decimal('base_salary', 18, 2)->default(0);
            $table->string('salary_period')->default('monthly');
            $table->decimal('working_hours_per_week', 6, 2)->default(44);
            $table->decimal('working_hours_per_day', 6, 2)->default(8);
            $table->boolean('overtime_allowed')->default(true);
            $table->boolean('night_shift_allowed')->default(false);
            $table->string('shift_work_type')->nullable();
            $table->decimal('housing_allowance', 18, 2)->default(0);
            $table->decimal('food_allowance', 18, 2)->default(0);
            $table->decimal('spouse_allowance', 18, 2)->default(0);
            $table->decimal('child_allowance', 18, 2)->default(0);
            $table->decimal('seniority_allowance', 18, 2)->default(0);
            $table->json('allowances_payload')->nullable();
            $table->json('deductions_payload')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id', 'status'], 'pir_contract_tenant_company_status_idx');
            $table->index(['employee_id', 'contract_type', 'status'], 'pir_contract_employee_type_status_idx');
            $table->index(['company_id', 'branch_id'], 'pir_contract_company_branch_idx');
        });

        Schema::create('payroll_ir_leave_types', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->boolean('is_paid')->default(true);
            $table->decimal('annual_quota_days', 6, 2)->default(0);
            $table->decimal('carryover_limit_days', 6, 2)->default(0);
            $table->boolean('requires_attachment')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'company_id', 'code'], 'pir_leave_type_code_unique');
            $table->index(['tenant_id', 'company_id', 'is_active'], 'pir_leave_type_active_idx');
        });

        Schema::create('payroll_ir_leave_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->foreignId('employee_id')->constrained('payroll_ir_employees')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('payroll_ir_leave_types')->cascadeOnDelete();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->string('status')->default('pending');
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'status'], 'pir_leave_request_employee_status_idx');
            $table->index(['company_id', 'branch_id', 'status'], 'pir_leave_request_company_status_idx');
            $table->index(['start_at', 'end_at'], 'pir_leave_request_date_idx');
        });

        Schema::create('payroll_ir_holidays', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->date('date');
            $table->string('title');
            $table->boolean('is_official')->default(true);
            $table->boolean('is_weekly')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'company_id', 'date'], 'pir_holiday_date_unique');
            $table->index(['company_id', 'date'], 'pir_holiday_company_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_ir_holidays');
        Schema::dropIfExists('payroll_ir_leave_requests');
        Schema::dropIfExists('payroll_ir_leave_types');
        Schema::dropIfExists('payroll_ir_contracts');
    }
};
