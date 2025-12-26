<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 120);
            $table->decimal('buy_price', 18, 2)->nullable();
            $table->decimal('sell_price', 18, 2)->nullable();
            $table->string('source', 50)->default('alanchand');
            $table->timestamp('fetched_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index('source');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};
