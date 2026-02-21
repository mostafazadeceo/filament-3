<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_pattern_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('provider_connection_id')->constrained('sms_bulk_provider_connections')->cascadeOnDelete();
            $table->string('pattern_code', 128)->index();
            $table->json('title_translations')->nullable();
            $table->json('variables_schema')->nullable();
            $table->string('status', 32)->default('pending')->index();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'provider_connection_id', 'pattern_code'], 'sms_bulk_pattern_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_pattern_templates');
    }
};
