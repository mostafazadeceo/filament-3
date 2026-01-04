<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workhub_ai_field_runs')) {
            return;
        }

        Schema::create('workhub_ai_field_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('workhub_custom_fields')->cascadeOnDelete();
            $table->foreignId('work_item_id')->constrained('workhub_work_items')->cascadeOnDelete();
            $table->json('output_json');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'field_id', 'work_item_id', 'created_at'], 'workhub_ai_field_runs_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workhub_ai_field_runs');
    }
};
