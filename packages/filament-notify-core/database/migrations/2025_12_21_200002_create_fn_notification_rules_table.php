<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fn_notification_rules', function (Blueprint $table) {
            $table->id();
            $table->string('panel_id')->index();
            $table->string('name');
            $table->boolean('enabled')->default(true)->index();
            $table->foreignId('trigger_id')->constrained('fn_triggers')->cascadeOnDelete();
            $table->json('conditions')->nullable();
            $table->json('recipients')->nullable();
            $table->json('channels')->nullable();
            $table->json('throttle')->nullable();
            $table->timestamps();

            $table->index(['panel_id', 'trigger_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fn_notification_rules');
    }
};
