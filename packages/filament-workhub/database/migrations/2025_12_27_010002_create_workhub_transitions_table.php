<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workhub_transitions')) {
            return;
        }

        Schema::create('workhub_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('workflow_id')->constrained('workhub_workflows')->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('from_status_id')->constrained('workhub_statuses')->cascadeOnDelete();
            $table->foreignId('to_status_id')->constrained('workhub_statuses')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('validators')->nullable();
            $table->json('post_actions')->nullable();
            $table->timestamps();

            $table->index(['workflow_id', 'from_status_id']);
            $table->index(['workflow_id', 'to_status_id']);
            $table->index(['tenant_id', 'workflow_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workhub_transitions');
    }
};
