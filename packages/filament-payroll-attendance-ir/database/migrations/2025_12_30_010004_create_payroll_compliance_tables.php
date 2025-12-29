<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_minimum_wage_tables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_min_wage_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_min_wage_company_fk')
                ->cascadeOnDelete();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->decimal('daily_wage', 18, 2)->default(0);
            $table->decimal('monthly_wage', 18, 2)->default(0);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'effective_from'], 'payroll_min_wage_tenant_company_effective_idx');
        });

        Schema::create('payroll_allowance_tables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_allow_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_allow_company_fk')
                ->cascadeOnDelete();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->decimal('housing_allowance', 18, 2)->default(0);
            $table->decimal('food_allowance', 18, 2)->default(0);
            $table->decimal('child_allowance_daily', 18, 2)->default(0);
            $table->decimal('marriage_allowance', 18, 2)->default(0);
            $table->decimal('seniority_allowance_daily', 18, 2)->default(0);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'effective_from'], 'payroll_allow_tenant_company_effective_idx');
        });

        Schema::create('payroll_insurance_tables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_ins_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_ins_company_fk')
                ->cascadeOnDelete();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->decimal('employee_rate', 6, 2)->default(7.00);
            $table->decimal('employer_rate', 6, 2)->default(23.00);
            $table->decimal('max_insurable_daily', 18, 2)->nullable();
            $table->decimal('max_insurable_monthly', 18, 2)->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'effective_from'], 'payroll_ins_tenant_company_effective_idx');
        });

        Schema::create('payroll_tax_tables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_tax_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_tax_company_fk')
                ->cascadeOnDelete();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->decimal('exemption_amount', 18, 2)->default(0);
            $table->decimal('flat_allowance_rate', 6, 2)->default(10.00);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'effective_from'], 'payroll_tax_tenant_company_effective_idx');
        });

        Schema::create('payroll_tax_brackets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_tax_bracket_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_tax_bracket_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('payroll_tax_table_id')
                ->constrained('payroll_tax_tables', 'id', 'payroll_tax_bracket_table_fk')
                ->cascadeOnDelete();
            $table->decimal('min_amount', 18, 2)->default(0);
            $table->decimal('max_amount', 18, 2)->nullable();
            $table->decimal('rate', 6, 2)->default(0);
            $table->timestamps();

            $table->index(['payroll_tax_table_id', 'min_amount'], 'payroll_tax_bracket_table_min_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_tax_brackets');
        Schema::dropIfExists('payroll_tax_tables');
        Schema::dropIfExists('payroll_insurance_tables');
        Schema::dropIfExists('payroll_allowance_tables');
        Schema::dropIfExists('payroll_minimum_wage_tables');
    }
};
