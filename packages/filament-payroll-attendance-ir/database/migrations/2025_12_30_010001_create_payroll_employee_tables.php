<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_employees', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_emp_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_emp_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_emp_branch_fk')
                ->nullOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users', 'id', 'payroll_emp_user_fk')
                ->nullOnDelete();
            $table->string('employee_no')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('national_id')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('marital_status')->default('single');
            $table->unsignedSmallInteger('children_count')->default(0);
            $table->date('employment_date')->nullable();
            $table->string('job_title')->nullable();
            $table->string('status')->default('active');
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_sheba')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'employee_no'], 'payroll_emp_company_no_uniq');
            $table->unique(['company_id', 'national_id'], 'payroll_emp_company_nid_uniq');
            $table->index(['tenant_id', 'company_id', 'branch_id', 'status'], 'payroll_emp_tenant_company_branch_status_idx');
        });

        Schema::create('payroll_employee_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_emp_doc_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_emp_doc_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('employee_id')
                ->constrained('payroll_employees', 'id', 'payroll_emp_doc_emp_fk')
                ->cascadeOnDelete();
            $table->string('document_type');
            $table->string('path');
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'employee_id'], 'payroll_emp_doc_tenant_company_emp_idx');
        });

        Schema::create('payroll_contracts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_contract_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_contract_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_contract_branch_fk')
                ->nullOnDelete();
            $table->foreignId('employee_id')
                ->constrained('payroll_employees', 'id', 'payroll_contract_emp_fk')
                ->cascadeOnDelete();
            $table->string('scope')->default('official');
            $table->string('status')->default('active');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->decimal('base_salary', 18, 2)->default(0);
            $table->decimal('daily_hours', 5, 2)->default(8);
            $table->decimal('weekly_hours', 5, 2)->default(44);
            $table->decimal('monthly_hours', 6, 2)->default(176);
            $table->boolean('overtime_allowed')->default(true);
            $table->boolean('night_shift_allowed')->default(true);
            $table->string('shift_type')->default('fixed');
            $table->decimal('housing_allowance', 18, 2)->default(0);
            $table->decimal('food_allowance', 18, 2)->default(0);
            $table->decimal('child_allowance', 18, 2)->default(0);
            $table->decimal('marriage_allowance', 18, 2)->default(0);
            $table->decimal('seniority_allowance', 18, 2)->default(0);
            $table->json('extra_allowances')->nullable();
            $table->boolean('insurance_included')->default(true);
            $table->boolean('tax_included')->default(true);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id', 'employee_id', 'scope', 'status'], 'payroll_contract_tenant_company_emp_scope_status_idx');
            $table->index(['employee_id', 'effective_from', 'effective_to'], 'payroll_contract_emp_effective_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_contracts');
        Schema::dropIfExists('payroll_employee_documents');
        Schema::dropIfExists('payroll_employees');
    }
};
