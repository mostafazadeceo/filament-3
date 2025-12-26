<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relograde_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')
                ->nullable()
                ->constrained('relograde_connections')
                ->nullOnDelete();
            $table->string('type');
            $table->string('severity')->default('warning');
            $table->string('currency')->nullable();
            $table->decimal('current_amount', 18, 4)->nullable();
            $table->decimal('threshold', 18, 4)->nullable();
            $table->string('message')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['connection_id', 'type']);
            $table->index(['severity', 'resolved_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relograde_alerts');
    }
};
