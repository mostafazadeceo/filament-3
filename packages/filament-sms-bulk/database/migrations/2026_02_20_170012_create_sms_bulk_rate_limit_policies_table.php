<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_rate_limit_policies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->unsignedInteger('per_minute')->nullable();
            $table->unsignedInteger('per_hour')->nullable();
            $table->unsignedInteger('per_day')->nullable();
            $table->unsignedInteger('burst')->nullable();
            $table->json('provider_limits_snapshot')->nullable();
            $table->timestamps();

            $table->unique('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_rate_limit_policies');
    }
};
