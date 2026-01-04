<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workhub_project_ai_updates')) {
            return;
        }

        Schema::create('workhub_project_ai_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('workhub_projects')->cascadeOnDelete();
            $table->string('status_enum')->default('on_track');
            $table->longText('body_markdown');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'project_id', 'created_at']);
            $table->index(['tenant_id', 'status_enum']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workhub_project_ai_updates');
    }
};
