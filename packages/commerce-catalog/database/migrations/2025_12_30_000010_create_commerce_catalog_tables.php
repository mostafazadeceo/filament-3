<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('commerce-catalog.tables', []);
        $productsTable = $tables['products'] ?? 'commerce_catalog_products';
        $variantsTable = $tables['variants'] ?? 'commerce_catalog_variants';
        $mediaTable = $tables['media'] ?? 'commerce_catalog_media';
        $collectionsTable = $tables['collections'] ?? 'commerce_catalog_collections';
        $collectionProductTable = $tables['collection_product'] ?? 'commerce_catalog_collection_product';

        if (! Schema::hasTable($productsTable)) {
            Schema::create($productsTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
                $table->string('name');
                $table->string('slug');
                $table->string('type')->default('physical');
                $table->string('status')->default('draft');
                $table->string('sku')->nullable();
                $table->text('summary')->nullable();
                $table->longText('description')->nullable();
                $table->string('currency', 8)->default('IRR');
                $table->decimal('price', 18, 4)->default(0);
                $table->decimal('compare_at_price', 18, 4)->nullable();
                $table->boolean('track_inventory')->default(true);
                $table->foreignId('accounting_product_id')->nullable()->constrained('accounting_ir_products_services')->nullOnDelete();
                $table->foreignId('inventory_item_id')->nullable()->constrained('accounting_ir_inventory_items')->nullOnDelete();
                $table->json('metadata')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['site_id', 'slug']);
                $table->index(['tenant_id', 'site_id', 'status']);
                $table->index(['tenant_id', 'site_id', 'type']);
                $table->index('updated_at');
            });
        }

        if (! Schema::hasTable($variantsTable)) {
            Schema::create($variantsTable, function (Blueprint $table) use ($productsTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained($productsTable)->cascadeOnDelete();
                $table->string('name');
                $table->string('sku')->nullable();
                $table->string('currency', 8)->default('IRR');
                $table->decimal('price', 18, 4)->default(0);
                $table->boolean('is_default')->default(false);
                $table->foreignId('inventory_item_id')->nullable()->constrained('accounting_ir_inventory_items')->nullOnDelete();
                $table->json('attributes')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'product_id']);
                $table->index(['product_id', 'is_default']);
            });
        }

        if (! Schema::hasTable($mediaTable)) {
            Schema::create($mediaTable, function (Blueprint $table) use ($productsTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained($productsTable)->cascadeOnDelete();
                $table->string('type')->default('image');
                $table->string('url');
                $table->string('alt')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_primary')->default(false);
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'product_id']);
                $table->index(['product_id', 'is_primary']);
            });
        }

        if (! Schema::hasTable($collectionsTable)) {
            Schema::create($collectionsTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
                $table->string('name');
                $table->string('slug');
                $table->string('status')->default('draft');
                $table->text('description')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['site_id', 'slug']);
                $table->index(['tenant_id', 'site_id', 'status']);
                $table->index('updated_at');
            });
        }

        if (! Schema::hasTable($collectionProductTable)) {
            Schema::create($collectionProductTable, function (Blueprint $table) use ($collectionsTable, $productsTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('collection_id')->constrained($collectionsTable)->cascadeOnDelete();
                $table->foreignId('product_id')->constrained($productsTable)->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['collection_id', 'product_id'], 'cc_collection_product_unique');
                $table->index(['tenant_id', 'collection_id'], 'cc_collection_product_tenant_collection_idx');
            });
        }

        if (Schema::hasTable($collectionProductTable)) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                $uniqueIndex = 'cc_collection_product_unique';
                $tenantIndex = 'cc_collection_product_tenant_collection_idx';

                $uniqueExists = DB::select("SHOW INDEX FROM {$collectionProductTable} WHERE Key_name = ?", [$uniqueIndex]);
                if (empty($uniqueExists)) {
                    Schema::table($collectionProductTable, function (Blueprint $table) use ($uniqueIndex) {
                        $table->unique(['collection_id', 'product_id'], $uniqueIndex);
                    });
                }

                $tenantExists = DB::select("SHOW INDEX FROM {$collectionProductTable} WHERE Key_name = ?", [$tenantIndex]);
                if (empty($tenantExists)) {
                    Schema::table($collectionProductTable, function (Blueprint $table) use ($tenantIndex) {
                        $table->index(['tenant_id', 'collection_id'], $tenantIndex);
                    });
                }
            }
        }
    }

    public function down(): void
    {
        $tables = config('commerce-catalog.tables', []);
        $collectionProductTable = $tables['collection_product'] ?? 'commerce_catalog_collection_product';
        $collectionsTable = $tables['collections'] ?? 'commerce_catalog_collections';
        $mediaTable = $tables['media'] ?? 'commerce_catalog_media';
        $variantsTable = $tables['variants'] ?? 'commerce_catalog_variants';
        $productsTable = $tables['products'] ?? 'commerce_catalog_products';

        Schema::dropIfExists($collectionProductTable);
        Schema::dropIfExists($collectionsTable);
        Schema::dropIfExists($mediaTable);
        Schema::dropIfExists($variantsTable);
        Schema::dropIfExists($productsTable);
    }
};
