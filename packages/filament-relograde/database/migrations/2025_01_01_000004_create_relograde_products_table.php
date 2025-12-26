<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relograde_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')
                ->constrained('relograde_connections')
                ->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->string('brand_slug')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('category')->nullable();
            $table->string('redeem_type')->nullable();
            $table->string('redeem_value')->nullable();
            $table->boolean('is_stocked')->default(false);
            $table->boolean('is_variable_product')->default(false);
            $table->string('face_value_currency')->nullable();
            $table->decimal('face_value_amount', 18, 4)->nullable();
            $table->decimal('face_value_min', 18, 4)->nullable();
            $table->decimal('face_value_max', 18, 4)->nullable();
            $table->decimal('price_amount', 18, 4)->nullable();
            $table->string('price_currency')->nullable();
            $table->decimal('fee_variable', 18, 4)->nullable();
            $table->decimal('fee_fixed', 18, 4)->nullable();
            $table->string('fee_currency')->nullable();
            $table->json('raw_json');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['connection_id', 'slug']);
            $table->index(['connection_id', 'brand_slug']);
            $table->index(['connection_id', 'category', 'redeem_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relograde_products');
    }
};
