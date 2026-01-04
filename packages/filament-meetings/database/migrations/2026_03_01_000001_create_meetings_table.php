<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('meetings')) {
            return;
        }

        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->dateTime('scheduled_at')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->string('location_type')->default('online');
            $table->string('location_value')->nullable();
            $table->foreignId('organizer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('draft');
            $table->boolean('ai_enabled')->default(false);
            $table->boolean('consent_required')->default(true);
            $table->string('consent_mode')->default('manual');
            $table->timestamp('consent_confirmed_at')->nullable();
            $table->foreignId('consent_confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('share_minutes_mode')->default('private');
            $table->string('minutes_format')->default('team');
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'scheduled_at']);
            $table->index(['tenant_id', 'organizer_id']);
            $table->index(['tenant_id', 'ai_enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
