<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relograde_connections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('environment', ['sandbox', 'production']);
            $table->text('api_key');
            $table->string('api_version')->default('1.02');
            $table->string('base_url')->default('https://connect.relograde.com');
            $table->text('webhook_secret')->nullable();
            $table->json('webhook_allowed_ips')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['environment', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relograde_connections');
    }
};
