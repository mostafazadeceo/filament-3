<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('loyalty_referral_programs')) {
            Schema::create('loyalty_referral_programs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('code_prefix')->nullable();
                $table->string('status')->default('active');
                $table->string('qualification_event')->default('purchase_completed');
                $table->decimal('min_purchase_amount', 16, 4)->nullable();
                $table->unsignedInteger('waiting_days')->default(14);
                $table->unsignedInteger('max_per_referrer')->nullable();
                $table->unsignedInteger('period_days')->nullable();
                $table->unsignedBigInteger('referrer_points')->default(0);
                $table->unsignedBigInteger('referee_points')->default(0);
                $table->decimal('referrer_cashback', 16, 4)->default(0);
                $table->decimal('referee_cashback', 16, 4)->default(0);
                $table->string('reward_type')->default('points');
                $table->json('fraud_rules')->nullable();
                $table->timestamp('valid_from')->nullable();
                $table->timestamp('valid_until')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable('loyalty_referrals')) {
            Schema::create('loyalty_referrals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('program_id')->constrained('loyalty_referral_programs')->cascadeOnDelete();
                $table->foreignId('referrer_customer_id')->constrained('loyalty_customers')->cascadeOnDelete();
                $table->foreignId('referee_customer_id')->nullable()->constrained('loyalty_customers')->nullOnDelete();
                $table->string('referral_code');
                $table->string('referee_phone')->nullable();
                $table->string('referee_email')->nullable();
                $table->string('status')->default('pending');
                $table->unsignedInteger('fraud_score')->default(0);
                $table->string('fraud_reason')->nullable();
                $table->timestamp('qualified_at')->nullable();
                $table->timestamp('reward_due_at')->nullable();
                $table->timestamp('rewarded_at')->nullable();
                $table->timestamp('flagged_at')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'referral_code']);
                $table->index(['tenant_id', 'referrer_customer_id']);
                $table->index(['tenant_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_referrals');
        Schema::dropIfExists('loyalty_referral_programs');
    }
};
