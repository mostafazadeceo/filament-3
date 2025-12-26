<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relograde_brand_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')
                ->constrained('relograde_brands')
                ->cascadeOnDelete();
            $table->string('redeem_value')->nullable();
            $table->json('raw_json');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relograde_brand_options');
    }
};
