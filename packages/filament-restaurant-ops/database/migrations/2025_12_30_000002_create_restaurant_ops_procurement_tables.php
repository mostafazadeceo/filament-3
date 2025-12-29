<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_purchase_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'rest_pr_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'rest_pr_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'rest_pr_branch_fk')
                ->nullOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users', 'id', 'rest_pr_user_fk')->nullOnDelete();
            $table->string('status')->default('draft');
            $table->date('needed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'branch_id'], 'rest_pr_tenant_company_branch_idx');
            $table->index(['company_id', 'status'], 'rest_pr_company_status_idx');
            $table->index(['company_id', 'needed_at'], 'rest_pr_company_needed_idx');
        });

        Schema::create('restaurant_purchase_request_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('purchase_request_id')
                ->constrained('restaurant_purchase_requests', 'id', 'rest_pr_lines_request_fk')
                ->cascadeOnDelete();
            $table->foreignId('item_id')
                ->constrained('restaurant_items', 'id', 'rest_pr_lines_item_fk')
                ->cascadeOnDelete();
            $table->foreignId('uom_id')
                ->nullable()
                ->constrained('restaurant_uoms', 'id', 'rest_pr_lines_uom_fk')
                ->nullOnDelete();
            $table->decimal('quantity', 14, 4);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['purchase_request_id', 'item_id'], 'rest_pr_lines_request_item_idx');
        });

        Schema::create('restaurant_purchase_orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'rest_po_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'rest_po_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'rest_po_branch_fk')
                ->nullOnDelete();
            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('restaurant_suppliers', 'id', 'rest_po_supplier_fk')
                ->nullOnDelete();
            $table->foreignId('purchase_request_id')
                ->nullable()
                ->constrained('restaurant_purchase_requests', 'id', 'rest_po_request_fk')
                ->nullOnDelete();
            $table->string('order_no')->nullable();
            $table->date('order_date')->nullable();
            $table->date('expected_at')->nullable();
            $table->string('status')->default('draft');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('tax_total', 14, 2)->default(0);
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'branch_id'], 'rest_po_tenant_company_branch_idx');
            $table->index(['company_id', 'status'], 'rest_po_company_status_idx');
            $table->index(['company_id', 'order_date'], 'rest_po_company_order_date_idx');
        });

        Schema::create('restaurant_purchase_order_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('purchase_order_id')
                ->constrained('restaurant_purchase_orders', 'id', 'rest_po_lines_order_fk')
                ->cascadeOnDelete();
            $table->foreignId('item_id')
                ->constrained('restaurant_items', 'id', 'rest_po_lines_item_fk')
                ->cascadeOnDelete();
            $table->foreignId('uom_id')
                ->nullable()
                ->constrained('restaurant_uoms', 'id', 'rest_po_lines_uom_fk')
                ->nullOnDelete();
            $table->decimal('quantity', 14, 4);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('tax_rate', 6, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2)->default(0);
            $table->timestamps();

            $table->index(['purchase_order_id', 'item_id'], 'rest_po_lines_order_item_idx');
        });

        Schema::create('restaurant_goods_receipts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'rest_gr_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'rest_gr_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'rest_gr_branch_fk')
                ->nullOnDelete();
            $table->foreignId('warehouse_id')
                ->constrained('restaurant_warehouses', 'id', 'rest_gr_warehouse_fk')
                ->cascadeOnDelete();
            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('restaurant_suppliers', 'id', 'rest_gr_supplier_fk')
                ->nullOnDelete();
            $table->foreignId('purchase_order_id')
                ->nullable()
                ->constrained('restaurant_purchase_orders', 'id', 'rest_gr_order_fk')
                ->nullOnDelete();
            $table->string('receipt_no')->nullable();
            $table->date('receipt_date')->nullable();
            $table->string('status')->default('draft');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('tax_total', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'branch_id'], 'rest_gr_tenant_company_branch_idx');
            $table->index(['company_id', 'status'], 'rest_gr_company_status_idx');
            $table->index(['company_id', 'receipt_date'], 'rest_gr_company_date_idx');
        });

        Schema::create('restaurant_goods_receipt_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('goods_receipt_id')
                ->constrained('restaurant_goods_receipts', 'id', 'rest_gr_lines_receipt_fk')
                ->cascadeOnDelete();
            $table->foreignId('item_id')
                ->constrained('restaurant_items', 'id', 'rest_gr_lines_item_fk')
                ->cascadeOnDelete();
            $table->foreignId('uom_id')
                ->nullable()
                ->constrained('restaurant_uoms', 'id', 'rest_gr_lines_uom_fk')
                ->nullOnDelete();
            $table->decimal('quantity', 14, 4);
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->decimal('tax_rate', 6, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->string('batch_no')->nullable();
            $table->date('expires_at')->nullable();
            $table->decimal('line_total', 14, 2)->default(0);
            $table->timestamps();

            $table->index(['goods_receipt_id', 'item_id'], 'rest_gr_lines_receipt_item_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_goods_receipt_lines');
        Schema::dropIfExists('restaurant_goods_receipts');
        Schema::dropIfExists('restaurant_purchase_order_lines');
        Schema::dropIfExists('restaurant_purchase_orders');
        Schema::dropIfExists('restaurant_purchase_request_lines');
        Schema::dropIfExists('restaurant_purchase_requests');
    }
};
