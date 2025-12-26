<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relograde_brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')
                ->constrained('relograde_connections')
                ->cascadeOnDelete();
            $table->string('slug');
            $table->string('brand_name');
            $table->string('category')->nullable();
            $table->string('redeem_type')->nullable();
            $table->json('raw_json');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['connection_id', 'slug']);
            $table->index(['connection_id', 'category', 'redeem_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relograde_brands');
    }
};
