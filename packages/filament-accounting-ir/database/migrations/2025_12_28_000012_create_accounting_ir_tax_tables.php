<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ir_tax_rates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('tax_type')->default('vat');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'code'], 'air_tax_rates_company_code_uniq');
        });

        Schema::create('accounting_ir_tax_rate_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tax_rate_id')->constrained('accounting_ir_tax_rates')->cascadeOnDelete();
            $table->decimal('rate', 8, 4)->default(0);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tax_rate_id', 'effective_from'], 'air_tax_rate_versions_rate_effective_idx');
        });

        Schema::create('accounting_ir_vat_periods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('fiscal_year_id')->constrained('accounting_ir_fiscal_years')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('status')->default('open');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'period_start'], 'air_vat_periods_tenant_company_start_idx');
        });

        Schema::create('accounting_ir_vat_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vat_period_id')->constrained('accounting_ir_vat_periods')->cascadeOnDelete();
            $table->decimal('sales_base', 18, 2)->default(0);
            $table->decimal('sales_tax', 18, 2)->default(0);
            $table->decimal('purchase_base', 18, 2)->default(0);
            $table->decimal('purchase_tax', 18, 2)->default(0);
            $table->string('status')->default('draft');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('accounting_ir_vat_report_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vat_report_id')->constrained('accounting_ir_vat_reports')->cascadeOnDelete();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('base_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('accounting_ir_withholding_rates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->decimal('rate', 8, 4)->default(0);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'code'], 'air_withholding_rates_company_code_uniq');
        });

        Schema::create('accounting_ir_withholding_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('party_id')->nullable()->constrained('accounting_ir_parties')->nullOnDelete();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('base_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->date('tax_date')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'tax_date'], 'air_withholding_items_tenant_company_date_idx');
        });

        Schema::create('accounting_ir_seasonal_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('status')->default('draft');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'period_start'], 'air_seasonal_reports_tenant_company_start_idx');
        });

        Schema::create('accounting_ir_seasonal_report_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seasonal_report_id')->constrained('accounting_ir_seasonal_reports')->cascadeOnDelete();
            $table->foreignId('party_id')->nullable()->constrained('accounting_ir_parties')->nullOnDelete();
            $table->string('invoice_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->decimal('amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('accounting_ir_seasonal_submissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seasonal_report_id')->constrained('accounting_ir_seasonal_reports')->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->string('status')->default('pending');
            $table->json('response_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ir_seasonal_submissions');
        Schema::dropIfExists('accounting_ir_seasonal_report_lines');
        Schema::dropIfExists('accounting_ir_seasonal_reports');
        Schema::dropIfExists('accounting_ir_withholding_items');
        Schema::dropIfExists('accounting_ir_withholding_rates');
        Schema::dropIfExists('accounting_ir_vat_report_lines');
        Schema::dropIfExists('accounting_ir_vat_reports');
        Schema::dropIfExists('accounting_ir_vat_periods');
        Schema::dropIfExists('accounting_ir_tax_rate_versions');
        Schema::dropIfExists('accounting_ir_tax_rates');
    }
};
