<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('providers-esim-go-core.tables.products', 'esim_go_products');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedBigInteger('catalog_product_id')->nullable();
            $table->unsignedBigInteger('catalog_variant_id')->nullable();
            $table->string('bundle_name');
            $table->string('provider_product_id')->nullable();
            $table->text('description')->nullable();
            $table->json('groups')->nullable();
            $table->json('countries')->nullable();
            $table->json('region')->nullable();
            $table->json('allowances')->nullable();
            $table->decimal('price', 18, 4)->default(0);
            $table->string('currency', 8)->default('USD');
            $table->integer('data_amount_mb')->nullable();
            $table->integer('duration_days')->nullable();
            $table->json('speed')->nullable();
            $table->boolean('autostart')->default(false);
            $table->boolean('unlimited')->default(false);
            $table->json('roaming_enabled')->nullable();
            $table->string('billing_type')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index(['tenant_id', 'bundle_name'], 'esim_go_products_bundle_idx');
            $table->index(['tenant_id', 'status'], 'esim_go_products_status_idx');
            $table->index('updated_at', 'esim_go_products_updated_idx');
        });
    }

    public function down(): void
    {
        $table = config('providers-esim-go-core.tables.products', 'esim_go_products');
        Schema::dropIfExists($table);
    }
};
