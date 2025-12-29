<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workhub_audit_events')) {
            return;
        }

        Schema::create('workhub_audit_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('workhub_projects')->nullOnDelete();
            $table->foreignId('work_item_id')->nullable()->constrained('workhub_work_items')->nullOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event');
            $table->json('payload')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'event', 'created_at']);
            $table->index(['project_id', 'created_at']);
            $table->index(['work_item_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workhub_audit_events');
    }
};
