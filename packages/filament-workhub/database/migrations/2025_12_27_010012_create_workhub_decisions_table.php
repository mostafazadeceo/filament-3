<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workhub_decisions')) {
            return;
        }

        Schema::create('workhub_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('work_item_id')->constrained('workhub_work_items')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->index(['work_item_id', 'decided_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workhub_decisions');
    }
};
