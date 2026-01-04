<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('commerce-orders.tables', []);
        $ordersTable = $tables['orders'] ?? 'commerce_orders';
        $itemsTable = $tables['order_items'] ?? 'commerce_order_items';
        $paymentsTable = $tables['order_payments'] ?? 'commerce_order_payments';
        $returnsTable = $tables['order_returns'] ?? 'commerce_order_returns';
        $returnItemsTable = $tables['order_return_items'] ?? 'commerce_order_return_items';
        $refundsTable = $tables['order_refunds'] ?? 'commerce_order_refunds';

        $catalogTables = config('commerce-catalog.tables', []);
        $productsTable = $catalogTables['products'] ?? 'commerce_catalog_products';
        $variantsTable = $catalogTables['variants'] ?? 'commerce_catalog_variants';

        if (! Schema::hasTable($returnsTable)) {
            Schema::create($returnsTable, function (Blueprint $table) use ($ordersTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('order_id')->constrained($ordersTable)->cascadeOnDelete();
                $table->string('status')->default('requested');
                $table->string('reason')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('requested_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->timestamp('received_at')->nullable();
                $table->timestamp('refunded_at')->nullable();
                $table->foreignId('requested_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('rejected_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('received_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('refunded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'order_id']);
                $table->index(['tenant_id', 'status']);
                $table->index('updated_at');
            });
        }

        if (! Schema::hasTable($returnItemsTable)) {
            Schema::create($returnItemsTable, function (Blueprint $table) use ($returnsTable, $itemsTable, $productsTable, $variantsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('order_return_id')->constrained($returnsTable)->cascadeOnDelete();
                $table->foreignId('order_item_id')->nullable()->constrained($itemsTable)->nullOnDelete();
                $table->foreignId('product_id')->nullable()->constrained($productsTable)->nullOnDelete();
                $table->foreignId('variant_id')->nullable()->constrained($variantsTable)->nullOnDelete();
                $table->string('name');
                $table->string('sku')->nullable();
                $table->decimal('quantity', 18, 4)->default(1);
                $table->string('reason')->nullable();
                $table->string('status')->default('requested');
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'order_return_id']);
                $table->index(['tenant_id', 'order_item_id']);
            });
        }

        if (! Schema::hasTable($refundsTable)) {
            Schema::create($refundsTable, function (Blueprint $table) use ($ordersTable, $returnsTable, $paymentsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('order_id')->constrained($ordersTable)->cascadeOnDelete();
                $table->foreignId('order_return_id')->nullable()->constrained($returnsTable)->nullOnDelete();
                $table->foreignId('order_payment_id')->nullable()->constrained($paymentsTable)->nullOnDelete();
                $table->string('status')->default('pending');
                $table->string('currency', 8)->default('IRR');
                $table->decimal('amount', 18, 4)->default(0);
                $table->string('provider')->nullable();
                $table->string('reference')->nullable();
                $table->string('reason')->nullable();
                $table->string('idempotency_key')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'idempotency_key']);
                $table->index(['tenant_id', 'order_id']);
                $table->index(['tenant_id', 'status']);
                $table->index('updated_at');
            });
        }
    }

    public function down(): void
    {
        $tables = config('commerce-orders.tables', []);
        $refundsTable = $tables['order_refunds'] ?? 'commerce_order_refunds';
        $returnItemsTable = $tables['order_return_items'] ?? 'commerce_order_return_items';
        $returnsTable = $tables['order_returns'] ?? 'commerce_order_returns';

        Schema::dropIfExists($refundsTable);
        Schema::dropIfExists($returnItemsTable);
        Schema::dropIfExists($returnsTable);
    }
};
