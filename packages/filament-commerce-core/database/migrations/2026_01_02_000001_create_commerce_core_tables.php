<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('filament-commerce-core.tables', []);
        $productsTable = $tables['products'] ?? 'commerce_products';
        $variantsTable = $tables['variants'] ?? 'commerce_variants';
        $categoriesTable = $tables['categories'] ?? 'commerce_categories';
        $brandsTable = $tables['brands'] ?? 'commerce_brands';
        $categoryProductTable = $tables['category_product'] ?? 'commerce_category_product';
        $priceListsTable = $tables['price_lists'] ?? 'commerce_price_lists';
        $pricesTable = $tables['prices'] ?? 'commerce_prices';
        $inventoryItemsTable = $tables['inventory_items'] ?? 'commerce_inventory_items';
        $stockMovesTable = $tables['stock_moves'] ?? 'commerce_stock_moves';
        $customersTable = $tables['customers'] ?? 'commerce_customers';

        if (! Schema::hasTable($brandsTable)) {
            Schema::create($brandsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('slug');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['tenant_id', 'slug']);
                $table->index(['tenant_id', 'is_active']);
                $table->index('updated_at');
            });
        }

        if (! Schema::hasTable($categoriesTable)) {
            Schema::create($categoriesTable, function (Blueprint $table) use ($categoriesTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('parent_id')->nullable()->constrained($categoriesTable)->nullOnDelete();
                $table->string('name');
                $table->string('slug');
                $table->text('description')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['tenant_id', 'slug']);
                $table->index(['tenant_id', 'parent_id']);
                $table->index(['tenant_id', 'is_active']);
            });
        }

        $sitesTable = config('site-builder-core.tables.sites', 'sites');

        if (! Schema::hasTable($productsTable)) {
            Schema::create($productsTable, function (Blueprint $table) use ($brandsTable, $sitesTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('site_id')->nullable()->constrained($sitesTable)->nullOnDelete();
                $table->foreignId('brand_id')->nullable()->constrained($brandsTable)->nullOnDelete();
                $table->string('name');
                $table->string('slug');
                $table->string('type')->default('simple');
                $table->string('status')->default('draft');
                $table->string('sku')->nullable();
                $table->text('summary')->nullable();
                $table->longText('description')->nullable();
                $table->string('currency', 10)->default('IRR');
                $table->decimal('price', 12, 4)->default(0);
                $table->decimal('compare_at_price', 12, 4)->nullable();
                $table->boolean('track_inventory')->default(true);
                $table->json('metadata')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['tenant_id', 'slug']);
                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'sku']);
                $table->index(['tenant_id', 'site_id']);
                $table->index('updated_at');
            });
        }

        if (! Schema::hasTable($variantsTable)) {
            Schema::create($variantsTable, function (Blueprint $table) use ($productsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained($productsTable)->cascadeOnDelete();
                $table->string('name')->nullable();
                $table->string('sku')->nullable();
                $table->string('barcode')->nullable();
                $table->string('status')->default('active');
                $table->string('currency', 10)->default('IRR');
                $table->decimal('price', 12, 4)->default(0);
                $table->decimal('compare_at_price', 12, 4)->nullable();
                $table->json('attributes')->nullable();
                $table->json('metadata')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['tenant_id', 'product_id']);
                $table->index(['tenant_id', 'sku']);
                $table->index(['tenant_id', 'status']);
                $table->index('updated_at');
            });
        }

        if (! Schema::hasTable($categoryProductTable)) {
            Schema::create($categoryProductTable, function (Blueprint $table) use ($categoriesTable, $productsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('category_id')->constrained($categoriesTable)->cascadeOnDelete();
                $table->foreignId('product_id')->constrained($productsTable)->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['tenant_id', 'category_id', 'product_id'], 'commerce_cat_prod_unique');
                $table->index(['tenant_id', 'category_id']);
                $table->index(['tenant_id', 'product_id']);
            });
        }

        if (! Schema::hasTable($priceListsTable)) {
            Schema::create($priceListsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('code');
                $table->string('currency', 10)->default('IRR');
                $table->string('status')->default('active');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'code']);
                $table->index(['tenant_id', 'status']);
                $table->index('updated_at');
            });
        }

        if (! Schema::hasTable($pricesTable)) {
            Schema::create($pricesTable, function (Blueprint $table) use ($priceListsTable, $productsTable, $variantsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('price_list_id')->constrained($priceListsTable)->cascadeOnDelete();
                $table->foreignId('product_id')->constrained($productsTable)->cascadeOnDelete();
                $table->foreignId('variant_id')->nullable()->constrained($variantsTable)->nullOnDelete();
                $table->string('currency', 10)->default('IRR');
                $table->decimal('price', 12, 4);
                $table->decimal('compare_at_price', 12, 4)->nullable();
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'price_list_id', 'product_id', 'variant_id'], 'commerce_prices_unique');
                $table->index(['tenant_id', 'price_list_id']);
                $table->index(['tenant_id', 'product_id']);
                $table->index(['tenant_id', 'variant_id']);
            });
        }

        if (! Schema::hasTable($inventoryItemsTable)) {
            Schema::create($inventoryItemsTable, function (Blueprint $table) use ($productsTable, $variantsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained($productsTable)->cascadeOnDelete();
                $table->foreignId('variant_id')->nullable()->constrained($variantsTable)->nullOnDelete();
                $table->string('sku')->nullable();
                $table->string('location_label')->nullable();
                $table->decimal('quantity_on_hand', 12, 4)->default(0);
                $table->decimal('quantity_reserved', 12, 4)->default(0);
                $table->string('status')->default('active');
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'product_id']);
                $table->index(['tenant_id', 'variant_id']);
                $table->index(['tenant_id', 'sku']);
                $table->index(['tenant_id', 'status']);
                $table->index('updated_at');
            });
        }

        if (! Schema::hasTable($stockMovesTable)) {
            Schema::create($stockMovesTable, function (Blueprint $table) use ($inventoryItemsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('inventory_item_id')->constrained($inventoryItemsTable)->cascadeOnDelete();
                $table->string('type');
                $table->decimal('quantity', 12, 4);
                $table->string('reason')->nullable();
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'inventory_item_id']);
                $table->index(['tenant_id', 'type']);
                $table->index('created_at');
            });
        }

        if (! Schema::hasTable($customersTable)) {
            Schema::create($customersTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('status')->default('active');
                $table->json('default_billing_address')->nullable();
                $table->json('default_shipping_address')->nullable();
                $table->text('notes')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'email']);
                $table->index(['tenant_id', 'phone']);
                $table->index('updated_at');
            });
        }
    }

    public function down(): void
    {
        $tables = config('filament-commerce-core.tables', []);
        $customersTable = $tables['customers'] ?? 'commerce_customers';
        $stockMovesTable = $tables['stock_moves'] ?? 'commerce_stock_moves';
        $inventoryItemsTable = $tables['inventory_items'] ?? 'commerce_inventory_items';
        $pricesTable = $tables['prices'] ?? 'commerce_prices';
        $priceListsTable = $tables['price_lists'] ?? 'commerce_price_lists';
        $categoryProductTable = $tables['category_product'] ?? 'commerce_category_product';
        $variantsTable = $tables['variants'] ?? 'commerce_variants';
        $productsTable = $tables['products'] ?? 'commerce_products';
        $categoriesTable = $tables['categories'] ?? 'commerce_categories';
        $brandsTable = $tables['brands'] ?? 'commerce_brands';

        Schema::dropIfExists($customersTable);
        Schema::dropIfExists($stockMovesTable);
        Schema::dropIfExists($inventoryItemsTable);
        Schema::dropIfExists($pricesTable);
        Schema::dropIfExists($priceListsTable);
        Schema::dropIfExists($categoryProductTable);
        Schema::dropIfExists($variantsTable);
        Schema::dropIfExists($productsTable);
        Schema::dropIfExists($categoriesTable);
        Schema::dropIfExists($brandsTable);
    }
};
