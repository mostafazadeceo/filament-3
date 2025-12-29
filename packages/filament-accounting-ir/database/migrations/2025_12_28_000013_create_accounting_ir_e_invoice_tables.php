<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ir_e_invoice_providers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('driver')->default('mock');
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'is_active'], 'air_e_invoice_providers_tenant_company_active_idx');
        });

        Schema::create('accounting_ir_key_materials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->string('material_type');
            $table->text('encrypted_value');
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'material_type'], 'air_key_materials_tenant_company_type_idx');
        });

        Schema::create('accounting_ir_e_invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('sales_invoice_id')->nullable()->constrained('accounting_ir_sales_invoices')->nullOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('accounting_ir_e_invoice_providers')->nullOnDelete();
            $table->string('invoice_type')->default('standard');
            $table->string('status')->default('draft');
            $table->string('unique_tax_id')->nullable();
            $table->string('payload_version')->default('v1');
            $table->timestamp('issued_at')->nullable();
            $table->json('payload')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'status'], 'air_e_invoices_tenant_company_status_idx');
        });

        Schema::create('accounting_ir_e_invoice_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('e_invoice_id')->constrained('accounting_ir_e_invoices')->cascadeOnDelete();
            $table->string('description')->nullable();
            $table->decimal('quantity', 18, 4)->default(1);
            $table->decimal('unit_price', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('line_total', 18, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('accounting_ir_e_invoice_submissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('e_invoice_id')->constrained('accounting_ir_e_invoices')->cascadeOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('accounting_ir_e_invoice_providers')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->string('correlation_id')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamps();
        });

        Schema::create('accounting_ir_e_invoice_status_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('e_invoice_id')->constrained('accounting_ir_e_invoices')->cascadeOnDelete();
            $table->string('status');
            $table->string('message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ir_e_invoice_status_logs');
        Schema::dropIfExists('accounting_ir_e_invoice_submissions');
        Schema::dropIfExists('accounting_ir_e_invoice_lines');
        Schema::dropIfExists('accounting_ir_e_invoices');
        Schema::dropIfExists('accounting_ir_key_materials');
        Schema::dropIfExists('accounting_ir_e_invoice_providers');
    }
};
