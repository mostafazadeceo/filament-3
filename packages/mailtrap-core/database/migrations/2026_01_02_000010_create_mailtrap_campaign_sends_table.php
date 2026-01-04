<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('mailtrap-core.tables.campaign_sends', 'mailtrap_campaign_sends');
        $campaigns = config('mailtrap-core.tables.campaigns', 'mailtrap_campaigns');
        $contacts = config('mailtrap-core.tables.audience_contacts', 'mailtrap_audience_contacts');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) use ($campaigns, $contacts) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained($campaigns)->cascadeOnDelete();
            $table->foreignId('audience_contact_id')->nullable()->constrained($contacts)->nullOnDelete();
            $table->string('email', 190);
            $table->string('name')->nullable();
            $table->string('status', 40)->default('pending');
            $table->string('provider_message_id')->nullable();
            $table->string('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'campaign_id', 'status'], 'mailtrap_campaign_sends_status_idx');
            $table->index(['tenant_id', 'email'], 'mailtrap_campaign_sends_email_idx');
        });
    }

    public function down(): void
    {
        $table = config('mailtrap-core.tables.campaign_sends', 'mailtrap_campaign_sends');
        Schema::dropIfExists($table);
    }
};
