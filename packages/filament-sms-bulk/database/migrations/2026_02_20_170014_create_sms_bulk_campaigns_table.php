<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('provider_connection_id')->constrained('sms_bulk_provider_connections')->cascadeOnDelete();
            $table->string('name');
            $table->string('mode', 32)->index();
            $table->string('language', 8)->default('fa')->index();
            $table->string('encoding', 16)->default('auto');
            $table->string('sender', 32)->nullable();
            $table->string('cost_center', 64)->nullable()->index();
            $table->timestamp('schedule_at')->nullable()->index();
            $table->foreignId('quiet_hours_profile_id')->nullable()->constrained('sms_bulk_quiet_hours_profiles')->nullOnDelete();
            $table->string('approval_state', 32)->default('draft')->index();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->decimal('cost_estimate', 14, 4)->nullable();
            $table->decimal('cost_final', 14, 4)->nullable();
            $table->json('pricing_snapshot')->nullable();
            $table->json('payload_snapshot')->nullable();
            $table->string('idempotency_key', 100)->nullable()->index();
            $table->string('status', 32)->default('draft')->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'updated_at']);
            $table->unique(['tenant_id', 'idempotency_key'], 'sms_bulk_campaigns_tenant_idempotency_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_campaigns');
    }
};
