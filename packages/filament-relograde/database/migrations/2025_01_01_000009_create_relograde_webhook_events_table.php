<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relograde_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')
                ->nullable()
                ->constrained('relograde_connections')
                ->nullOnDelete();
            $table->string('event');
            $table->string('state')->nullable();
            $table->string('api_key_description')->nullable();
            $table->string('trx')->nullable();
            $table->string('reference')->nullable();
            $table->json('payload');
            $table->string('received_ip')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('processing_status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['processing_status', 'processed_at']);
            $table->index(['trx', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relograde_webhook_events');
    }
};
