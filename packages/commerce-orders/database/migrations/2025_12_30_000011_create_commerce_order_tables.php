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

        $catalogTables = config('commerce-catalog.tables', []);
        $productsTable = $catalogTables['products'] ?? 'commerce_catalog_products';
        $variantsTable = $catalogTables['variants'] ?? 'commerce_catalog_variants';

        if (! Schema::hasTable($ordersTable)) {
            Schema::create($ordersTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedBigInteger('cart_id')->nullable();
                $table->string('number')->nullable();
                $table->string('status')->default('pending');
                $table->string('payment_status')->default('pending');
                $table->string('currency', 8)->default('IRR');
                $table->decimal('subtotal', 18, 4)->default(0);
                $table->decimal('discount_total', 18, 4)->default(0);
                $table->decimal('tax_total', 18, 4)->default(0);
                $table->decimal('shipping_total', 18, 4)->default(0);
                $table->decimal('total', 18, 4)->default(0);
                $table->string('customer_name')->nullable();
                $table->string('customer_email')->nullable();
                $table->string('customer_phone')->nullable();
                $table->json('billing_address')->nullable();
                $table->json('shipping_address')->nullable();
                $table->text('customer_note')->nullable();
                $table->text('internal_note')->nullable();
                $table->string('idempotency_key')->nullable();
                $table->json('meta')->nullable();
                $table->timestamp('placed_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamp('fulfilled_at')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'idempotency_key']);
                $table->unique(['tenant_id', 'number']);
                $table->index(['tenant_id', 'site_id', 'status']);
                $table->index(['tenant_id', 'site_id', 'payment_status']);
                $table->index(['tenant_id', 'user_id']);
                $table->index('updated_at');
            });
        }

        if (! Schema::hasTable($itemsTable)) {
            Schema::create($itemsTable, function (Blueprint $table) use ($ordersTable, $productsTable, $variantsTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('order_id')->constrained($ordersTable)->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained($productsTable)->nullOnDelete();
                $table->foreignId('variant_id')->nullable()->constrained($variantsTable)->nullOnDelete();
                $table->string('name');
                $table->string('sku')->nullable();
                $table->decimal('quantity', 18, 4)->default(1);
                $table->string('currency', 8)->default('IRR');
                $table->decimal('unit_price', 18, 4)->default(0);
                $table->decimal('line_total', 18, 4)->default(0);
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'order_id']);
            });
        }

        if (! Schema::hasTable($paymentsTable)) {
            Schema::create($paymentsTable, function (Blueprint $table) use ($ordersTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('order_id')->constrained($ordersTable)->cascadeOnDelete();
                $table->string('method')->default('wallet');
                $table->string('status')->default('pending');
                $table->string('currency', 8)->default('IRR');
                $table->decimal('amount', 18, 4)->default(0);
                $table->string('provider')->nullable();
                $table->string('reference')->nullable();
                $table->foreignId('wallet_transaction_id')->nullable()->constrained('wallet_transactions')->nullOnDelete();
                $table->foreignId('wallet_hold_id')->nullable()->constrained('wallet_holds')->nullOnDelete();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'order_id']);
                $table->index(['tenant_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        $tables = config('commerce-orders.tables', []);
        $paymentsTable = $tables['order_payments'] ?? 'commerce_order_payments';
        $itemsTable = $tables['order_items'] ?? 'commerce_order_items';
        $ordersTable = $tables['orders'] ?? 'commerce_orders';

        Schema::dropIfExists($paymentsTable);
        Schema::dropIfExists($itemsTable);
        Schema::dropIfExists($ordersTable);
    }
};
