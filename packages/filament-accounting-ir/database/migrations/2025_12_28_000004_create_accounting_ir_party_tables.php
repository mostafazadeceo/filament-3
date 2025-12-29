<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ir_parties', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->string('party_type')->default('customer');
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('national_id')->nullable();
            $table->string('economic_code')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id', 'party_type'], 'air_parties_tenant_company_type_idx');
            $table->index(['tenant_id', 'national_id'], 'air_parties_tenant_national_idx');
        });

        Schema::create('accounting_ir_party_tax_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('party_id')->constrained('accounting_ir_parties')->cascadeOnDelete();
            $table->string('tax_type')->default('vat');
            $table->decimal('rate', 8, 4)->nullable();
            $table->string('exemption_code')->nullable();
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'party_id'], 'air_party_tax_tenant_company_party_idx');
            $table->index(['tax_type', 'effective_from'], 'air_party_tax_type_effective_idx');
        });

        Schema::create('accounting_ir_party_addresses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('party_id')->constrained('accounting_ir_parties')->cascadeOnDelete();
            $table->string('address_type')->default('billing');
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('phone')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'party_id'], 'air_party_addr_tenant_company_party_idx');
        });

        Schema::create('accounting_ir_uoms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('accounting_ir_companies')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('accounting_ir_tax_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->decimal('vat_rate', 8, 4)->default(0);
            $table->boolean('is_exempt')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'code'], 'air_tax_categories_company_code_uniq');
        });

        Schema::create('accounting_ir_products_services', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('item_type')->default('product');
            $table->foreignId('uom_id')->nullable()->constrained('accounting_ir_uoms')->nullOnDelete();
            $table->foreignId('tax_category_id')->nullable()->constrained('accounting_ir_tax_categories')->nullOnDelete();
            $table->decimal('base_price', 18, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id', 'item_type'], 'air_products_tenant_company_type_idx');
            $table->index(['tenant_id', 'is_active'], 'air_products_tenant_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ir_products_services');
        Schema::dropIfExists('accounting_ir_tax_categories');
        Schema::dropIfExists('accounting_ir_uoms');
        Schema::dropIfExists('accounting_ir_party_addresses');
        Schema::dropIfExists('accounting_ir_party_tax_profiles');
        Schema::dropIfExists('accounting_ir_parties');
    }
};
