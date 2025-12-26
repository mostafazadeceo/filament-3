<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relograde_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')
                ->constrained('relograde_order_items')
                ->cascadeOnDelete();
            $table->string('tag')->nullable();
            $table->string('status')->nullable();
            $table->text('voucher_code')->nullable();
            $table->string('voucher_serial')->nullable();
            $table->timestamp('voucher_date_expired')->nullable();
            $table->string('token')->nullable();
            $table->string('voucher_url')->nullable();
            $table->json('raw_json');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relograde_order_lines');
    }
};
