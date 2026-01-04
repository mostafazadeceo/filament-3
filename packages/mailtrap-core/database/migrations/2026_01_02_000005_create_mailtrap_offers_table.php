<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('mailtrap-core.tables.offers', 'mailtrap_offers');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('status')->default('inactive');
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_days')->default(30);
            $table->json('feature_keys')->nullable();
            $table->json('limits')->nullable();
            $table->decimal('price', 12, 4)->default(0);
            $table->string('currency', 8)->default('IRR');
            $table->unsignedBigInteger('catalog_product_id')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'slug'], 'mailtrap_offer_slug_unique');
            $table->index(['tenant_id', 'status'], 'mailtrap_offer_status_idx');
            $table->index('catalog_product_id', 'mailtrap_offer_catalog_idx');
        });
    }

    public function down(): void
    {
        $table = config('mailtrap-core.tables.offers', 'mailtrap_offers');
        Schema::dropIfExists($table);
    }
};
