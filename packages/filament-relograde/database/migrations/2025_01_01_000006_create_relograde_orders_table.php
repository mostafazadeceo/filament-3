<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relograde_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')
                ->constrained('relograde_connections')
                ->cascadeOnDelete();
            $table->string('trx')->unique();
            $table->string('reference')->nullable();
            $table->string('state')->nullable();
            $table->string('type')->default('api');
            $table->string('order_status')->nullable();
            $table->string('payment_status')->nullable();
            $table->boolean('is_balance_payment')->default(true);
            $table->boolean('downloaded')->default(false);
            $table->string('payment_currency')->nullable();
            $table->string('price_currency')->nullable();
            $table->decimal('price_amount', 18, 4)->nullable();
            $table->decimal('price_vat', 18, 4)->nullable();
            $table->decimal('price_incl_vat', 18, 4)->nullable();
            $table->decimal('price_fx', 18, 6)->nullable();
            $table->timestamp('date_created')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['connection_id', 'order_status']);
            $table->index(['connection_id', 'state']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relograde_orders');
    }
};
