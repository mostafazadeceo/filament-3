<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ai_policies')) {
            return;
        }

        Schema::create('ai_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->boolean('enabled')->default(false);
            $table->string('provider')->default('mock');
            $table->json('redaction_policy')->nullable();
            $table->unsignedInteger('retention_days')->default(30);
            $table->boolean('consent_required_meetings')->default(true);
            $table->boolean('allow_store_transcripts')->default(false);
            $table->timestamps();

            $table->unique(['tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_policies');
    }
};
