<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_consent_registries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('msisdn', 32)->index();
            $table->string('status', 16)->default('unknown')->index();
            $table->string('source', 32)->nullable();
            $table->timestamp('consented_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'msisdn'], 'sms_bulk_consent_tenant_msisdn_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_consent_registries');
    }
};
