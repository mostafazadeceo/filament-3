<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('api_key_scopes')) {
            return;
        }

        Schema::create('api_key_scopes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_key_id')->constrained('api_keys')->cascadeOnDelete();
            $table->string('scope');
            $table->timestamps();

            $table->unique(['api_key_id', 'scope']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_key_scopes');
    }
};
