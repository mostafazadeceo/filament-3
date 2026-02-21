<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_bulk_campaign_recipients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('campaign_id')->constrained('sms_bulk_campaigns')->cascadeOnDelete();
            $table->string('msisdn', 32)->index();
            $table->json('variables')->nullable();
            $table->string('remote_message_id', 128)->nullable()->index();
            $table->string('status', 32)->default('queued')->index();
            $table->timestamp('delivered_at')->nullable();
            $table->unsignedSmallInteger('parts_count')->nullable();
            $table->decimal('cost', 14, 4)->nullable();
            $table->string('error_code', 64)->nullable();
            $table->text('error_message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'campaign_id', 'status']);
            $table->unique(['tenant_id', 'campaign_id', 'msisdn'], 'sms_bulk_recipients_campaign_msisdn_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_campaign_recipients');
    }
};
