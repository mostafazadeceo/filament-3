<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ir_inventory_warehouses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id'], 'air_inv_warehouses_tenant_company_idx');
        });

        Schema::create('accounting_ir_inventory_locations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('accounting_ir_inventory_warehouses')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['warehouse_id', 'code'], 'air_inv_locations_warehouse_code_idx');
        });

        Schema::create('accounting_ir_inventory_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('accounting_ir_products_services')->nullOnDelete();
            $table->string('sku')->nullable();
            $table->decimal('min_stock', 18, 4)->default(0);
            $table->decimal('current_stock', 18, 4)->default(0);
            $table->boolean('allow_negative')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'product_id'], 'air_inv_items_tenant_company_product_idx');
        });

        Schema::create('accounting_ir_inventory_docs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('accounting_ir_inventory_warehouses')->nullOnDelete();
            $table->string('doc_type')->default('receipt');
            $table->string('doc_no')->nullable();
            $table->date('doc_date');
            $table->string('status')->default('draft');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'doc_date'], 'air_inv_docs_tenant_company_date_idx');
            $table->index(['tenant_id', 'status'], 'air_inv_docs_tenant_status_idx');
        });

        Schema::create('accounting_ir_inventory_doc_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('inventory_doc_id')->constrained('accounting_ir_inventory_docs')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('accounting_ir_inventory_items')->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('accounting_ir_inventory_locations')->nullOnDelete();
            $table->decimal('quantity', 18, 4)->default(0);
            $table->decimal('unit_cost', 18, 4)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['inventory_doc_id', 'inventory_item_id'], 'air_inv_doc_lines_doc_item_idx');
        });

        Schema::create('accounting_ir_stock_moves', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('accounting_ir_inventory_items')->cascadeOnDelete();
            $table->foreignId('inventory_doc_id')->nullable()->constrained('accounting_ir_inventory_docs')->nullOnDelete();
            $table->decimal('quantity', 18, 4)->default(0);
            $table->decimal('unit_cost', 18, 4)->nullable();
            $table->string('direction')->default('in');
            $table->date('move_date');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'move_date'], 'air_stock_moves_tenant_company_date_idx');
            $table->index(['inventory_item_id'], 'air_stock_moves_item_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ir_stock_moves');
        Schema::dropIfExists('accounting_ir_inventory_doc_lines');
        Schema::dropIfExists('accounting_ir_inventory_docs');
        Schema::dropIfExists('accounting_ir_inventory_items');
        Schema::dropIfExists('accounting_ir_inventory_locations');
        Schema::dropIfExists('accounting_ir_inventory_warehouses');
    }
};
