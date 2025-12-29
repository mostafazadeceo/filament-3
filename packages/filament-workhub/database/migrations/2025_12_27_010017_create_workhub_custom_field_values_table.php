<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workhub_custom_field_values')) {
            return;
        }

        Schema::create('workhub_custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('workhub_custom_fields')->cascadeOnDelete();
            $table->foreignId('work_item_id')->nullable()->constrained('workhub_work_items')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('workhub_projects')->cascadeOnDelete();
            $table->json('value')->nullable();
            $table->timestamps();

            $table->unique(['field_id', 'work_item_id', 'project_id'], 'workhub_custom_field_value_unique');
            $table->index(['tenant_id', 'field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workhub_custom_field_values');
    }
};
