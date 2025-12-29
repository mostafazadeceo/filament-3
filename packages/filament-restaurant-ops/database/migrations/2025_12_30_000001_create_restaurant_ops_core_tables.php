<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_suppliers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'rest_suppliers_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'rest_suppliers_company_fk')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('status')->default('active');
            $table->string('contact_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('payment_terms')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'name'], 'rest_suppliers_company_name_uniq');
            $table->index(['tenant_id', 'company_id', 'status'], 'rest_suppliers_tenant_company_status_idx');
        });

        Schema::create('restaurant_uoms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'rest_uoms_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'rest_uoms_company_fk')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('symbol')->nullable();
            $table->boolean('is_base')->default(false);
            $table->timestamps();

            $table->unique(['company_id', 'name'], 'rest_uoms_company_name_uniq');
            $table->index(['tenant_id', 'company_id'], 'rest_uoms_tenant_company_idx');
        });

        Schema::create('restaurant_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'rest_items_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'rest_items_company_fk')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('base_uom_id')
                ->nullable()
                ->constrained('restaurant_uoms', 'id', 'rest_items_base_uom_fk');
            $table->foreignId('purchase_uom_id')
                ->nullable()
                ->constrained('restaurant_uoms', 'id', 'rest_items_purchase_uom_fk');
            $table->foreignId('consumption_uom_id')
                ->nullable()
                ->constrained('restaurant_uoms', 'id', 'rest_items_consumption_uom_fk');
            $table->decimal('purchase_to_base_rate', 12, 4)->default(1);
            $table->decimal('consumption_to_base_rate', 12, 4)->default(1);
            $table->decimal('min_stock', 14, 4)->nullable();
            $table->decimal('max_stock', 14, 4)->nullable();
            $table->decimal('reorder_point', 14, 4)->nullable();
            $table->boolean('track_batch')->default(false);
            $table->boolean('track_expiry')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'name'], 'rest_items_company_name_uniq');
            $table->index(['tenant_id', 'company_id', 'is_active'], 'rest_items_tenant_company_active_idx');
            $table->index(['company_id', 'category'], 'rest_items_company_category_idx');
        });

        Schema::create('restaurant_warehouses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'rest_wh_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'rest_wh_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'rest_wh_branch_fk')
                ->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('type')->default('main');
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'name'], 'rest_wh_company_name_uniq');
            $table->index(['tenant_id', 'company_id', 'branch_id'], 'rest_wh_tenant_company_branch_idx');
            $table->index(['company_id', 'is_active'], 'rest_wh_company_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_warehouses');
        Schema::dropIfExists('restaurant_items');
        Schema::dropIfExists('restaurant_uoms');
        Schema::dropIfExists('restaurant_suppliers');
    }
};
