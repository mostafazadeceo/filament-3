<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency_rate_runs', function (Blueprint $table) {
            $table->id();
            $table->string('source', 50)->default('alanchand');
            $table->string('status', 20)->default('success');
            $table->unsignedInteger('rates_count')->default(0);
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('fetched_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_rate_runs');
    }
};
