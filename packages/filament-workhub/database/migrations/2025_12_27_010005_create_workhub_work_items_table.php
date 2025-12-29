<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workhub_work_items')) {
            return;
        }

        Schema::create('workhub_work_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('workhub_projects')->cascadeOnDelete();
            $table->foreignId('work_type_id')->nullable()->constrained('workhub_work_types')->nullOnDelete();
            $table->foreignId('workflow_id')->constrained('workhub_workflows')->restrictOnDelete();
            $table->foreignId('status_id')->constrained('workhub_statuses')->restrictOnDelete();
            $table->unsignedInteger('number');
            $table->string('key', 32);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium');
            $table->foreignId('reporter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('estimate_minutes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['project_id', 'number']);
            $table->unique(['tenant_id', 'key']);
            $table->index(['tenant_id', 'project_id']);
            $table->index(['tenant_id', 'status_id']);
            $table->index(['tenant_id', 'assignee_id']);
            $table->index(['tenant_id', 'due_date']);
            $table->index(['tenant_id', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workhub_work_items');
    }
};
