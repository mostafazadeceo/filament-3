<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_inventory_docs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'rest_inv_docs_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'rest_inv_docs_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'rest_inv_docs_branch_fk')
                ->nullOnDelete();
            $table->foreignId('warehouse_id')
                ->constrained('restaurant_warehouses', 'id', 'rest_inv_docs_warehouse_fk')
                ->cascadeOnDelete();
            $table->string('doc_no')->nullable();
            $table->string('doc_type')->default('receipt');
            $table->string('status')->default('draft');
            $table->date('doc_date')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'branch_id'], 'rest_inv_docs_tenant_company_branch_idx');
            $table->index(['company_id', 'warehouse_id', 'doc_date'], 'rest_inv_docs_company_wh_date_idx');
            $table->index(['company_id', 'status'], 'rest_inv_docs_company_status_idx');
        });

        Schema::create('restaurant_inventory_doc_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('inventory_doc_id')
                ->constrained('restaurant_inventory_docs', 'id', 'rest_inv_lines_doc_fk')
                ->cascadeOnDelete();
            $table->foreignId('item_id')
                ->constrained('restaurant_items', 'id', 'rest_inv_lines_item_fk')
                ->cascadeOnDelete();
            $table->foreignId('uom_id')
                ->nullable()
                ->constrained('restaurant_uoms', 'id', 'rest_inv_lines_uom_fk')
                ->nullOnDelete();
            $table->decimal('quantity', 14, 4);
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->string('batch_no')->nullable();
            $table->date('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['inventory_doc_id', 'item_id'], 'rest_inv_lines_doc_item_idx');
        });

        Schema::create('restaurant_stock_moves', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'rest_moves_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'rest_moves_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('warehouse_id')
                ->constrained('restaurant_warehouses', 'id', 'rest_moves_warehouse_fk')
                ->cascadeOnDelete();
            $table->foreignId('item_id')
                ->constrained('restaurant_items', 'id', 'rest_moves_item_fk')
                ->cascadeOnDelete();
            $table->foreignId('inventory_doc_id')
                ->nullable()
                ->constrained('restaurant_inventory_docs', 'id', 'rest_moves_doc_fk')
                ->nullOnDelete();
            $table->string('direction')->default('in');
            $table->decimal('quantity', 14, 4);
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->date('move_date')->nullable();
            $table->string('batch_no')->nullable();
            $table->date('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'warehouse_id'], 'rest_moves_tenant_company_wh_idx');
            $table->index(['item_id', 'move_date'], 'rest_moves_item_date_idx');
        });

        Schema::create('restaurant_inventory_balances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'rest_balances_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'rest_balances_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('warehouse_id')
                ->constrained('restaurant_warehouses', 'id', 'rest_balances_warehouse_fk')
                ->cascadeOnDelete();
            $table->foreignId('item_id')
                ->constrained('restaurant_items', 'id', 'rest_balances_item_fk')
                ->cascadeOnDelete();
            $table->decimal('quantity', 14, 4)->default(0);
            $table->timestamps();

            $table->unique(['warehouse_id', 'item_id'], 'rest_balances_wh_item_uniq');
            $table->index(['tenant_id', 'company_id'], 'rest_balances_tenant_company_idx');
        });

        Schema::create('restaurant_inventory_lots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'rest_lots_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'rest_lots_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('warehouse_id')
                ->constrained('restaurant_warehouses', 'id', 'rest_lots_warehouse_fk')
                ->cascadeOnDelete();
            $table->foreignId('item_id')
                ->constrained('restaurant_items', 'id', 'rest_lots_item_fk')
                ->cascadeOnDelete();
            $table->string('batch_no')->nullable();
            $table->date('expires_at')->nullable();
            $table->decimal('quantity', 14, 4)->default(0);
            $table->timestamps();

            $table->index(['warehouse_id', 'item_id'], 'rest_lots_wh_item_idx');
            $table->index(['item_id', 'expires_at'], 'rest_lots_item_exp_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_inventory_lots');
        Schema::dropIfExists('restaurant_inventory_balances');
        Schema::dropIfExists('restaurant_stock_moves');
        Schema::dropIfExists('restaurant_inventory_doc_lines');
        Schema::dropIfExists('restaurant_inventory_docs');
    }
};
