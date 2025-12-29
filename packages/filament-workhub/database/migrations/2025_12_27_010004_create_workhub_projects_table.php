<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workhub_projects')) {
            return;
        }

        Schema::create('workhub_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('workflow_id')->constrained('workhub_workflows')->restrictOnDelete();
            $table->string('key', 16);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('lead_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'key']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'updated_at']);
            $table->index(['workflow_id']);
            $table->index(['lead_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workhub_projects');
    }
};
