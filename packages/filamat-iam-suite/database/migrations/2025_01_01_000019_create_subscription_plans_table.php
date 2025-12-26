<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subscription_plans')) {
            return;
        }

        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('scope')->default('system');
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('price', 18, 2)->default(0);
            $table->string('currency')->default('irr');
            $table->unsignedInteger('period_days')->default(30);
            $table->unsignedInteger('trial_days')->default(0);
            $table->unsignedInteger('seat_limit')->nullable();
            $table->unsignedInteger('storage_limit')->nullable();
            $table->unsignedInteger('module_limit')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
