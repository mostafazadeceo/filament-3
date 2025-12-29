<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workhub_label_work_item')) {
            return;
        }

        Schema::create('workhub_label_work_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('label_id')->constrained('workhub_labels')->cascadeOnDelete();
            $table->foreignId('work_item_id')->constrained('workhub_work_items')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['label_id', 'work_item_id']);
            $table->index(['tenant_id', 'work_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workhub_label_work_item');
    }
};
