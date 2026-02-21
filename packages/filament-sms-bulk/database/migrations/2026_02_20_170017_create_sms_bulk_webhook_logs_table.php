<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_webhook_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('event', 64)->index();
            $table->json('payload')->nullable();
            $table->boolean('signature_valid')->nullable()->index();
            $table->timestamp('processed_at')->nullable();
            $table->string('status', 32)->default('received')->index();
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_webhook_logs');
    }
};
