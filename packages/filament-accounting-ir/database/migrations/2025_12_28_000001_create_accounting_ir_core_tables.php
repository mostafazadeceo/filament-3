<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ir_companies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('national_id')->nullable();
            $table->string('economic_code')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('timezone')->default('Asia/Tehran');
            $table->string('base_currency')->default('IRR');
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'name'], 'air_companies_tenant_name_uniq');
            $table->index(['tenant_id', 'is_active'], 'air_companies_tenant_active_idx');
        });

        Schema::create('accounting_ir_branches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code'], 'air_branches_company_code_uniq');
            $table->index(['tenant_id', 'company_id'], 'air_branches_tenant_company_idx');
            $table->index(['tenant_id', 'is_active'], 'air_branches_tenant_active_idx');
        });

        Schema::create('accounting_ir_fiscal_years', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_closed')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'name'], 'air_fiscal_years_company_name_uniq');
            $table->index(['tenant_id', 'company_id', 'start_date'], 'air_fiscal_years_tenant_company_start_idx');
        });

        Schema::create('accounting_ir_fiscal_periods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('fiscal_year_id')->constrained('accounting_ir_fiscal_years')->cascadeOnDelete();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('period_type')->default('month');
            $table->boolean('is_closed')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['fiscal_year_id', 'name'], 'air_fiscal_periods_year_name_uniq');
            $table->index(['tenant_id', 'company_id', 'start_date'], 'air_fiscal_periods_tenant_company_start_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ir_fiscal_periods');
        Schema::dropIfExists('accounting_ir_fiscal_years');
        Schema::dropIfExists('accounting_ir_branches');
        Schema::dropIfExists('accounting_ir_companies');
    }
};
