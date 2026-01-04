<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('loyalty_rewards')) {
            Schema::create('loyalty_rewards', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('type');
                $table->text('description')->nullable();
                $table->unsignedBigInteger('points_cost')->default(0);
                $table->decimal('cashback_cost', 16, 4)->default(0);
                $table->unsignedInteger('inventory')->nullable();
                $table->string('status')->default('active');
                $table->timestamp('valid_from')->nullable();
                $table->timestamp('valid_until')->nullable();
                $table->json('constraints')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'type']);
                $table->index(['tenant_id', 'valid_until']);
            });
        }

        if (! Schema::hasTable('loyalty_reward_redemptions')) {
            Schema::create('loyalty_reward_redemptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('reward_id')->constrained('loyalty_rewards')->cascadeOnDelete();
                $table->foreignId('customer_id')->constrained('loyalty_customers')->cascadeOnDelete();
                $table->unsignedBigInteger('points_spent')->default(0);
                $table->decimal('cashback_spent', 16, 4)->default(0);
                $table->string('idempotency_key')->nullable();
                $table->string('status')->default('redeemed');
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->json('meta')->nullable();
                $table->timestamp('redeemed_at')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'idempotency_key']);
                $table->index(['tenant_id', 'customer_id']);
                $table->index(['tenant_id', 'reward_id']);
            });
        }

        if (! Schema::hasTable('loyalty_coupons')) {
            Schema::create('loyalty_coupons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('reward_id')->nullable()->constrained('loyalty_rewards')->nullOnDelete();
                $table->foreignId('issued_to_customer_id')->nullable()->constrained('loyalty_customers')->nullOnDelete();
                $table->string('code');
                $table->string('type')->default('discount');
                $table->string('discount_type')->nullable();
                $table->decimal('discount_value', 16, 4)->nullable();
                $table->string('currency')->nullable();
                $table->unsignedInteger('max_uses')->nullable();
                $table->unsignedInteger('max_uses_per_customer')->nullable();
                $table->unsignedInteger('used_count')->default(0);
                $table->boolean('stackable')->default(false);
                $table->string('status')->default('active');
                $table->string('source')->nullable();
                $table->timestamp('valid_from')->nullable();
                $table->timestamp('valid_until')->nullable();
                $table->json('constraints')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'code']);
                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'valid_until']);
                $table->index(['tenant_id', 'issued_to_customer_id']);
            });
        }

        if (! Schema::hasTable('loyalty_coupon_redemptions')) {
            Schema::create('loyalty_coupon_redemptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('coupon_id')->constrained('loyalty_coupons')->cascadeOnDelete();
                $table->foreignId('customer_id')->nullable()->constrained('loyalty_customers')->nullOnDelete();
                $table->string('order_reference')->nullable();
                $table->string('status')->default('redeemed');
                $table->json('meta')->nullable();
                $table->timestamp('redeemed_at')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'coupon_id']);
                $table->index(['tenant_id', 'customer_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_coupon_redemptions');
        Schema::dropIfExists('loyalty_coupons');
        Schema::dropIfExists('loyalty_reward_redemptions');
        Schema::dropIfExists('loyalty_rewards');
    }
};
