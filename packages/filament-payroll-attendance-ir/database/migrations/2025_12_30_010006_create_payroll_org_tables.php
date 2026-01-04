<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_departments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_dept_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_dept_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_dept_branch_fk')
                ->nullOnDelete();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('payroll_departments', 'id', 'payroll_dept_parent_fk')
                ->nullOnDelete();
            $table->foreignId('manager_id')
                ->nullable()
                ->constrained('payroll_employees', 'id', 'payroll_dept_manager_fk')
                ->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'name'], 'payroll_dept_company_name_uniq');
            $table->index(['tenant_id', 'company_id', 'branch_id', 'is_active'], 'payroll_dept_tenant_company_branch_active_idx');
        });

        Schema::create('payroll_positions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_pos_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_pos_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('payroll_departments', 'id', 'payroll_pos_dept_fk')
                ->nullOnDelete();
            $table->string('title');
            $table->string('code')->nullable();
            $table->string('grade')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'title'], 'payroll_pos_company_title_uniq');
            $table->index(['tenant_id', 'company_id', 'department_id', 'is_active'], 'payroll_pos_tenant_company_dept_active_idx');
        });

        Schema::table('payroll_employees', function (Blueprint $table): void {
            $table->foreignId('department_id')
                ->nullable()
                ->after('branch_id')
                ->constrained('payroll_departments', 'id', 'payroll_emp_dept_fk')
                ->nullOnDelete();
            $table->foreignId('position_id')
                ->nullable()
                ->after('department_id')
                ->constrained('payroll_positions', 'id', 'payroll_emp_pos_fk')
                ->nullOnDelete();

            $table->index(['department_id', 'position_id'], 'payroll_emp_dept_pos_idx');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_employees', function (Blueprint $table): void {
            $table->dropIndex('payroll_emp_dept_pos_idx');
            $table->dropForeign('payroll_emp_pos_fk');
            $table->dropForeign('payroll_emp_dept_fk');
            $table->dropColumn(['department_id', 'position_id']);
        });

        Schema::dropIfExists('payroll_positions');
        Schema::dropIfExists('payroll_departments');
    }
};
