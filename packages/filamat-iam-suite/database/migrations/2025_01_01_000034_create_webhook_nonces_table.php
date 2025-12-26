<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('webhook_nonces')) {
            return;
        }

        Schema::create('webhook_nonces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->nullable()->constrained('webhooks')->nullOnDelete();
            $table->string('source')->default('generic');
            $table->string('nonce', 64);
            $table->unsignedBigInteger('timestamp');
            $table->timestamps();

            $table->unique(['source', 'nonce']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_nonces');
    }
};
