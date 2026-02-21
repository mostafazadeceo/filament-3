<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_routing_policies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('primary_connection_id')->nullable()->constrained('sms_bulk_provider_connections')->nullOnDelete();
            $table->foreignId('fallback_connection_id')->nullable()->constrained('sms_bulk_provider_connections')->nullOnDelete();
            $table->boolean('enabled')->default(true)->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_routing_policies');
    }
};
