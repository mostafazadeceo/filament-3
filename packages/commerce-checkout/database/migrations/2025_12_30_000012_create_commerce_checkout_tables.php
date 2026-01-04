<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('commerce-checkout.tables', []);
        $cartsTable = $tables['carts'] ?? 'commerce_checkout_carts';
        $itemsTable = $tables['cart_items'] ?? 'commerce_checkout_cart_items';

        $catalogTables = config('commerce-catalog.tables', []);
        $productsTable = $catalogTables['products'] ?? 'commerce_catalog_products';
        $variantsTable = $catalogTables['variants'] ?? 'commerce_catalog_variants';

        if (! Schema::hasTable($cartsTable)) {
            Schema::create($cartsTable, function (Blueprint $table) {
                $table->id();
                $table->uuid('public_id')->unique();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status')->default('active');
                $table->string('currency', 8)->default('IRR');
                $table->decimal('subtotal', 18, 4)->default(0);
                $table->decimal('discount_total', 18, 4)->default(0);
                $table->decimal('tax_total', 18, 4)->default(0);
                $table->decimal('shipping_total', 18, 4)->default(0);
                $table->decimal('total', 18, 4)->default(0);
                $table->json('meta')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'site_id', 'status']);
                $table->index(['tenant_id', 'user_id']);
                $table->index('updated_at');
            });
        }

        if (! Schema::hasTable($itemsTable)) {
            Schema::create($itemsTable, function (Blueprint $table) use ($cartsTable, $productsTable, $variantsTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('cart_id')->constrained($cartsTable)->cascadeOnDelete();
                $table->foreignId('product_id')->constrained($productsTable)->cascadeOnDelete();
                $table->foreignId('variant_id')->nullable()->constrained($variantsTable)->nullOnDelete();
                $table->string('name');
                $table->string('sku')->nullable();
                $table->decimal('quantity', 18, 4)->default(1);
                $table->string('currency', 8)->default('IRR');
                $table->decimal('unit_price', 18, 4)->default(0);
                $table->decimal('line_total', 18, 4)->default(0);
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['cart_id', 'product_id', 'variant_id'], 'cc_cart_items_cart_product_variant_uq');
                $table->index(['tenant_id', 'cart_id'], 'cc_cart_items_tenant_cart_idx');
            });
        }

        if (Schema::hasTable($itemsTable)) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                $uniqueIndex = 'cc_cart_items_cart_product_variant_uq';
                $tenantIndex = 'cc_cart_items_tenant_cart_idx';

                $uniqueExists = DB::select("SHOW INDEX FROM {$itemsTable} WHERE Key_name = ?", [$uniqueIndex]);
                if (empty($uniqueExists)) {
                    Schema::table($itemsTable, function (Blueprint $table) use ($uniqueIndex) {
                        $table->unique(['cart_id', 'product_id', 'variant_id'], $uniqueIndex);
                    });
                }

                $tenantExists = DB::select("SHOW INDEX FROM {$itemsTable} WHERE Key_name = ?", [$tenantIndex]);
                if (empty($tenantExists)) {
                    Schema::table($itemsTable, function (Blueprint $table) use ($tenantIndex) {
                        $table->index(['tenant_id', 'cart_id'], $tenantIndex);
                    });
                }
            }
        }
    }

    public function down(): void
    {
        $tables = config('commerce-checkout.tables', []);
        $itemsTable = $tables['cart_items'] ?? 'commerce_checkout_cart_items';
        $cartsTable = $tables['carts'] ?? 'commerce_checkout_carts';

        Schema::dropIfExists($itemsTable);
        Schema::dropIfExists($cartsTable);
    }
};
