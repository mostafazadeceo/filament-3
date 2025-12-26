<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relograde_api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')
                ->nullable()
                ->constrained('relograde_connections')
                ->nullOnDelete();
            $table->string('method', 10);
            $table->string('url');
            $table->string('endpoint_name')->nullable();
            $table->json('request_headers')->nullable();
            $table->json('request_body')->nullable();
            $table->unsignedInteger('response_status')->nullable();
            $table->json('response_body')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['connection_id', 'endpoint_name']);
            $table->index(['response_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relograde_api_logs');
    }
};
