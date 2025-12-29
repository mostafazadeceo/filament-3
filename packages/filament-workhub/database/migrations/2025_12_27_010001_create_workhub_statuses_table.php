<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workhub_statuses')) {
            return;
        }

        Schema::create('workhub_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('workflow_id')->constrained('workhub_workflows')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->enum('category', ['todo', 'in_progress', 'done']);
            $table->string('color', 20)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['workflow_id', 'slug']);
            $table->index(['tenant_id', 'workflow_id', 'category', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workhub_statuses');
    }
};
