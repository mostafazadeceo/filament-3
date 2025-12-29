<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workhub_time_entries')) {
            return;
        }

        Schema::create('workhub_time_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('work_item_id')->constrained('workhub_work_items')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('minutes');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['work_item_id', 'user_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workhub_time_entries');
    }
};
