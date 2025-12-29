<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workhub_automation_rules')) {
            return;
        }

        Schema::create('workhub_automation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('workhub_projects')->nullOnDelete();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->string('trigger_type');
            $table->json('trigger_config')->nullable();
            $table->json('conditions')->nullable();
            $table->json('actions')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
            $table->index(['project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workhub_automation_rules');
    }
};
