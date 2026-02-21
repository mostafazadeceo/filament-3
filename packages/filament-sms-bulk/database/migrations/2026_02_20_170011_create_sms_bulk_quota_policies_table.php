<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_quota_policies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->unsignedInteger('max_daily_recipients')->nullable();
            $table->unsignedInteger('max_monthly_recipients')->nullable();
            $table->decimal('max_daily_spend', 14, 4)->nullable();
            $table->decimal('max_monthly_spend', 14, 4)->nullable();
            $table->decimal('requires_approval_over_amount', 14, 4)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_quota_policies');
    }
};
