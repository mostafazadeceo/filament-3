<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_provider_connections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->string('provider', 64)->index();
            $table->string('display_name');
            $table->string('base_url_override')->nullable();
            $table->text('encrypted_token')->nullable();
            $table->string('default_sender', 32)->nullable();
            $table->string('status', 32)->default('active')->index();
            $table->timestamp('last_tested_at')->nullable();
            $table->decimal('last_credit_snapshot', 14, 4)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_provider_connections');
    }
};
