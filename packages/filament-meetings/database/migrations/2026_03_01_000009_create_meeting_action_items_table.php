<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('meeting_action_items')) {
            return;
        }

        Schema::create('meeting_action_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('meeting_id')->constrained('meetings')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->string('priority')->nullable();
            $table->string('status')->default('open');
            $table->foreignId('linked_workhub_item_id')->nullable()->constrained('workhub_work_items')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'meeting_id']);
            $table->index(['tenant_id', 'assignee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_action_items');
    }
};
