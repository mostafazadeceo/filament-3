<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('mailtrap-core.tables.audience_contacts', 'mailtrap_audience_contacts');
        $audiences = config('mailtrap-core.tables.audiences', 'mailtrap_audiences');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) use ($audiences) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('audience_id')->constrained($audiences)->cascadeOnDelete();
            $table->string('email', 190);
            $table->string('name')->nullable();
            $table->string('status', 40)->default('subscribed');
            $table->timestamp('unsubscribed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['audience_id', 'email'], 'mailtrap_audience_contacts_unique');
            $table->index(['tenant_id', 'email'], 'mailtrap_audience_contacts_email_idx');
            $table->index(['tenant_id', 'status'], 'mailtrap_audience_contacts_status_idx');
        });
    }

    public function down(): void
    {
        $table = config('mailtrap-core.tables.audience_contacts', 'mailtrap_audience_contacts');
        Schema::dropIfExists($table);
    }
};
