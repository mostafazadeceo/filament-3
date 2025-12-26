<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relograde_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('relograde_orders')
                ->cascadeOnDelete();
            $table->string('product_slug')->nullable();
            $table->string('product_name')->nullable();
            $table->string('brand')->nullable();
            $table->string('product_type')->nullable();
            $table->string('region')->nullable();
            $table->string('redeem_type')->nullable();
            $table->string('main_category')->nullable();
            $table->unsignedInteger('amount')->default(1);
            $table->decimal('face_value_amount', 18, 4)->nullable();
            $table->string('face_value_currency')->nullable();
            $table->decimal('face_value_fx', 18, 6)->nullable();
            $table->decimal('single_price_amount', 18, 4)->nullable();
            $table->decimal('total_price_amount', 18, 4)->nullable();
            $table->decimal('total_price_vat', 18, 4)->nullable();
            $table->decimal('total_price_incl_vat', 18, 4)->nullable();
            $table->decimal('price_fx', 18, 6)->nullable();
            $table->string('payment_currency')->nullable();
            $table->decimal('single_price_amount_in_payment_currency', 18, 4)->nullable();
            $table->decimal('total_price_amount_in_payment_currency', 18, 4)->nullable();
            $table->decimal('total_price_vat_in_payment_currency', 18, 4)->nullable();
            $table->decimal('total_price_incl_vat_in_payment_currency', 18, 4)->nullable();
            $table->unsignedInteger('lines_completed')->default(0);
            $table->json('raw_json');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relograde_order_items');
    }
};
