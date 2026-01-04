<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('meeting_templates')) {
            return;
        }

        Schema::create('meeting_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('format')->default('team');
            $table->string('scope')->default('workspace');
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('sections_enabled_json')->nullable();
            $table->json('custom_prompts_json')->nullable();
            $table->json('minutes_schema_json')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'scope']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_templates');
    }
};
