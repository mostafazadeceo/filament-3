<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_run_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_run_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_run_branch_fk')
                ->nullOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('status')->default('draft');
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users', 'id', 'payroll_run_approved_fk')
                ->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('posted_at')->nullable();
            $table->dateTime('locked_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'branch_id', 'period_start', 'period_end'], 'payroll_run_company_branch_period_uniq');
            $table->index(['tenant_id', 'company_id', 'branch_id', 'status'], 'payroll_run_tenant_company_branch_status_idx');
        });

        Schema::create('payroll_slips', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_slip_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_slip_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_slip_branch_fk')
                ->nullOnDelete();
            $table->foreignId('payroll_run_id')
                ->constrained('payroll_runs', 'id', 'payroll_slip_run_fk')
                ->cascadeOnDelete();
            $table->foreignId('employee_id')
                ->constrained('payroll_employees', 'id', 'payroll_slip_emp_fk')
                ->cascadeOnDelete();
            $table->string('scope')->default('official');
            $table->string('status')->default('draft');
            $table->decimal('gross_amount', 18, 2)->default(0);
            $table->decimal('deductions_amount', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->decimal('insurance_employee_amount', 18, 2)->default(0);
            $table->decimal('insurance_employer_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->dateTime('issued_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['payroll_run_id', 'employee_id', 'scope'], 'payroll_slip_run_emp_scope_uniq');
            $table->index(['tenant_id', 'company_id', 'branch_id', 'status'], 'payroll_slip_tenant_company_branch_status_idx');
        });

        Schema::create('payroll_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_item_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_item_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('payroll_slip_id')
                ->constrained('payroll_slips', 'id', 'payroll_item_slip_fk')
                ->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('type')->default('earning');
            $table->decimal('amount', 18, 2)->default(0);
            $table->string('tax_method')->default('progressive');
            $table->decimal('tax_rate', 6, 2)->nullable();
            $table->boolean('is_insurable')->default(true);
            $table->boolean('is_recurring')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['payroll_slip_id', 'type'], 'payroll_item_slip_type_idx');
            $table->index(['tenant_id', 'company_id'], 'payroll_item_tenant_company_idx');
        });

        Schema::create('payroll_loans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_loan_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_loan_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_loan_branch_fk')
                ->nullOnDelete();
            $table->foreignId('employee_id')
                ->constrained('payroll_employees', 'id', 'payroll_loan_emp_fk')
                ->cascadeOnDelete();
            $table->decimal('amount', 18, 2)->default(0);
            $table->unsignedInteger('installment_count')->default(1);
            $table->decimal('installment_amount', 18, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'branch_id', 'status'], 'payroll_loan_tenant_company_branch_status_idx');
        });

        Schema::create('payroll_loan_installments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_loan_inst_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_loan_inst_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('loan_id')
                ->constrained('payroll_loans', 'id', 'payroll_loan_inst_loan_fk')
                ->cascadeOnDelete();
            $table->date('due_date');
            $table->decimal('amount', 18, 2)->default(0);
            $table->dateTime('paid_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['loan_id', 'due_date'], 'payroll_loan_inst_loan_date_idx');
        });

        Schema::create('payroll_advances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_adv_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_adv_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_adv_branch_fk')
                ->nullOnDelete();
            $table->foreignId('employee_id')
                ->constrained('payroll_employees', 'id', 'payroll_adv_emp_fk')
                ->cascadeOnDelete();
            $table->decimal('amount', 18, 2)->default(0);
            $table->date('advance_date')->nullable();
            $table->string('status')->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'branch_id', 'status'], 'payroll_adv_tenant_company_branch_status_idx');
        });

        Schema::create('payroll_settlements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_settle_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_settle_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_settle_branch_fk')
                ->nullOnDelete();
            $table->foreignId('employee_id')
                ->constrained('payroll_employees', 'id', 'payroll_settle_emp_fk')
                ->cascadeOnDelete();
            $table->date('settlement_date')->nullable();
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'branch_id'], 'payroll_settle_tenant_company_branch_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_settlements');
        Schema::dropIfExists('payroll_advances');
        Schema::dropIfExists('payroll_loan_installments');
        Schema::dropIfExists('payroll_loans');
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payroll_slips');
        Schema::dropIfExists('payroll_runs');
    }
};
