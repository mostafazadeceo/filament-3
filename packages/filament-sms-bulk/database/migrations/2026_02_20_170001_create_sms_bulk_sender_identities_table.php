<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_sender_identities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('provider_connection_id')->constrained('sms_bulk_provider_connections')->cascadeOnDelete();
            $table->string('sender', 32);
            $table->string('label')->nullable();
            $table->string('status', 32)->default('active')->index();
            $table->timestamps();

            $table->unique(['tenant_id', 'provider_connection_id', 'sender'], 'sms_bulk_sender_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_sender_identities');
    }
};
