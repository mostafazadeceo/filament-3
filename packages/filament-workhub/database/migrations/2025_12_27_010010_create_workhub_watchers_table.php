<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workhub_watchers')) {
            return;
        }

        Schema::create('workhub_watchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('work_item_id')->constrained('workhub_work_items')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['work_item_id', 'user_id']);
            $table->index(['tenant_id', 'work_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workhub_watchers');
    }
};
