<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workhub_custom_fields')) {
            return;
        }

        Schema::create('workhub_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('scope');
            $table->string('name');
            $table->string('key');
            $table->string('type');
            $table->json('settings')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'scope', 'key']);
            $table->index(['tenant_id', 'scope', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workhub_custom_fields');
    }
};
