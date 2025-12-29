<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ir_sales_invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->foreignId('fiscal_year_id')->constrained('accounting_ir_fiscal_years')->cascadeOnDelete();
            $table->foreignId('party_id')->constrained('accounting_ir_parties')->cascadeOnDelete();
            $table->string('invoice_no');
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->string('status')->default('draft');
            $table->string('currency')->default('IRR');
            $table->decimal('exchange_rate', 18, 6)->nullable();
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('discount_total', 18, 2)->default(0);
            $table->decimal('tax_total', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);
            $table->boolean('is_official')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'fiscal_year_id', 'invoice_no'], 'air_sales_invoices_company_year_no_uniq');
            $table->index(['tenant_id', 'company_id', 'party_id'], 'air_sales_invoices_tenant_company_party_idx');
            $table->index(['tenant_id', 'status'], 'air_sales_invoices_tenant_status_idx');
        });

        Schema::create('accounting_ir_sales_invoice_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained('accounting_ir_sales_invoices')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('accounting_ir_products_services')->nullOnDelete();
            $table->string('description')->nullable();
            $table->decimal('quantity', 18, 4)->default(1);
            $table->decimal('unit_price', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_rate', 8, 4)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('line_total', 18, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['sales_invoice_id', 'product_id'], 'air_sales_lines_invoice_product_idx');
        });

        Schema::create('accounting_ir_sales_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained('accounting_ir_sales_invoices')->cascadeOnDelete();
            $table->foreignId('treasury_account_id')->nullable()->constrained('accounting_ir_treasury_accounts')->nullOnDelete();
            $table->date('paid_at')->nullable();
            $table->decimal('amount', 18, 2)->default(0);
            $table->string('method')->nullable();
            $table->string('reference')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('accounting_ir_sales_allocations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sales_payment_id')->constrained('accounting_ir_sales_payments')->cascadeOnDelete();
            $table->foreignId('sales_invoice_id')->constrained('accounting_ir_sales_invoices')->cascadeOnDelete();
            $table->decimal('amount', 18, 2)->default(0);
            $table->timestamps();

            $table->index(['sales_payment_id', 'sales_invoice_id'], 'air_sales_allocations_payment_invoice_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ir_sales_allocations');
        Schema::dropIfExists('accounting_ir_sales_payments');
        Schema::dropIfExists('accounting_ir_sales_invoice_lines');
        Schema::dropIfExists('accounting_ir_sales_invoices');
    }
};
