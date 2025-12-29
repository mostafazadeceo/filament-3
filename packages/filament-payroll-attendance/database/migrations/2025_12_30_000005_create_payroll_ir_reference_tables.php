<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_ir_insurance_tables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->decimal('employee_rate', 6, 4)->default(0.07);
            $table->decimal('employer_rate', 6, 4)->default(0.23);
            $table->decimal('min_daily_wage', 18, 2)->default(0);
            $table->decimal('max_daily_wage', 18, 2)->default(0);
            $table->decimal('max_insurable_daily_wage', 18, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'effective_from'], 'pir_insurance_table_effective_idx');
        });

        Schema::create('payroll_ir_tax_tables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->decimal('monthly_exemption', 18, 2)->default(0);
            $table->decimal('flat_extras_rate', 6, 4)->default(0.10);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'effective_from'], 'pir_tax_table_effective_idx');
        });

        Schema::create('payroll_ir_tax_brackets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tax_table_id')->constrained('payroll_ir_tax_tables')->cascadeOnDelete();
            $table->decimal('from_amount', 18, 2)->default(0);
            $table->decimal('to_amount', 18, 2)->nullable();
            $table->decimal('rate', 6, 4)->default(0);
            $table->timestamps();

            $table->index(['tax_table_id', 'from_amount'], 'pir_tax_bracket_from_idx');
        });

        Schema::create('payroll_ir_wage_tables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->decimal('min_daily_wage', 18, 2)->default(0);
            $table->decimal('min_monthly_wage', 18, 2)->default(0);
            $table->decimal('housing_allowance', 18, 2)->default(0);
            $table->decimal('food_allowance', 18, 2)->default(0);
            $table->decimal('spouse_allowance', 18, 2)->default(0);
            $table->decimal('child_allowance', 18, 2)->default(0);
            $table->decimal('seniority_daily', 18, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'effective_from'], 'pir_wage_table_effective_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_ir_wage_tables');
        Schema::dropIfExists('payroll_ir_tax_brackets');
        Schema::dropIfExists('payroll_ir_tax_tables');
        Schema::dropIfExists('payroll_ir_insurance_tables');
    }
};
