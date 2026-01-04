<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('loyalty_badges')) {
            Schema::create('loyalty_badges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('icon')->nullable();
                $table->text('description')->nullable();
                $table->string('status')->default('active');
                $table->json('perks')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable('loyalty_missions')) {
            Schema::create('loyalty_missions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('badge_id')->nullable()->constrained('loyalty_badges')->nullOnDelete();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('type')->default('count');
                $table->string('status')->default('active');
                $table->json('criteria')->nullable();
                $table->unsignedBigInteger('reward_points')->default(0);
                $table->decimal('reward_cashback', 16, 4)->default(0);
                $table->timestamp('start_at')->nullable();
                $table->timestamp('end_at')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'start_at']);
            });
        }

        if (! Schema::hasTable('loyalty_mission_progress')) {
            Schema::create('loyalty_mission_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('mission_id')->constrained('loyalty_missions')->cascadeOnDelete();
                $table->foreignId('customer_id')->constrained('loyalty_customers')->cascadeOnDelete();
                $table->unsignedInteger('progress')->default(0);
                $table->unsignedInteger('target')->default(0);
                $table->string('status')->default('in_progress');
                $table->timestamp('completed_at')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'mission_id', 'customer_id']);
                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable('loyalty_badge_awards')) {
            Schema::create('loyalty_badge_awards', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('badge_id')->constrained('loyalty_badges')->cascadeOnDelete();
                $table->foreignId('customer_id')->constrained('loyalty_customers')->cascadeOnDelete();
                $table->string('source')->nullable();
                $table->timestamp('awarded_at')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'badge_id', 'customer_id']);
                $table->index(['tenant_id', 'awarded_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_badge_awards');
        Schema::dropIfExists('loyalty_mission_progress');
        Schema::dropIfExists('loyalty_missions');
        Schema::dropIfExists('loyalty_badges');
    }
};
