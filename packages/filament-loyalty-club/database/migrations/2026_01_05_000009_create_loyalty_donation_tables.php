<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('loyalty_donation_pledges')) {
            Schema::create('loyalty_donation_pledges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('customer_id')->constrained('loyalty_customers')->cascadeOnDelete();
                $table->foreignId('reward_id')->constrained('loyalty_rewards')->cascadeOnDelete();
                $table->foreignId('redemption_id')->constrained('loyalty_reward_redemptions')->cascadeOnDelete();
                $table->unsignedBigInteger('points_spent')->default(0);
                $table->decimal('cashback_spent', 16, 4)->default(0);
                $table->string('charity_name')->nullable();
                $table->string('charity_reference')->nullable();
                $table->string('status')->default('pledged');
                $table->timestamp('pledged_at')->nullable();
                $table->timestamp('fulfilled_at')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'customer_id']);
                $table->index(['tenant_id', 'reward_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_donation_pledges');
    }
};
