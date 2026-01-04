<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('loyalty_wallet_accounts')) {
            Schema::create('loyalty_wallet_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('customer_id')->constrained('loyalty_customers')->cascadeOnDelete();
                $table->unsignedBigInteger('points_balance')->default(0);
                $table->unsignedBigInteger('points_earned_total')->default(0);
                $table->unsignedBigInteger('points_redeemed_total')->default(0);
                $table->decimal('cashback_balance', 16, 4)->default(0);
                $table->decimal('cashback_earned_total', 16, 4)->default(0);
                $table->decimal('cashback_redeemed_total', 16, 4)->default(0);
                $table->timestamps();

                $table->unique(['tenant_id', 'customer_id']);
                $table->index(['tenant_id', 'points_balance']);
                $table->index(['tenant_id', 'cashback_balance']);
            });
        }

        if (! Schema::hasTable('loyalty_wallet_ledgers')) {
            Schema::create('loyalty_wallet_ledgers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('customer_id')->constrained('loyalty_customers')->cascadeOnDelete();
                $table->foreignId('event_id')->nullable()->constrained('loyalty_events')->nullOnDelete();
                $table->string('type');
                $table->bigInteger('points_delta')->default(0);
                $table->decimal('cashback_delta', 16, 4)->default(0);
                $table->unsignedBigInteger('balance_after_points')->default(0);
                $table->decimal('balance_after_cashback', 16, 4)->default(0);
                $table->string('status')->default('posted');
                $table->string('idempotency_key')->nullable();
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->unsignedBigInteger('reversal_of_id')->nullable();
                $table->json('meta')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'idempotency_key']);
                $table->index(['tenant_id', 'customer_id']);
                $table->index(['tenant_id', 'type']);
                $table->index(['tenant_id', 'status']);
                $table->index(['expires_at']);
            });
        }

        if (! Schema::hasTable('loyalty_points_buckets')) {
            Schema::create('loyalty_points_buckets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('customer_id')->constrained('loyalty_customers')->cascadeOnDelete();
                $table->foreignId('ledger_id')->constrained('loyalty_wallet_ledgers')->cascadeOnDelete();
                $table->unsignedBigInteger('points_total');
                $table->unsignedBigInteger('points_available');
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'customer_id']);
                $table->index(['tenant_id', 'expires_at']);
            });
        }

        if (! Schema::hasTable('loyalty_points_consumptions')) {
            Schema::create('loyalty_points_consumptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('customer_id')->constrained('loyalty_customers')->cascadeOnDelete();
                $table->foreignId('bucket_id')->constrained('loyalty_points_buckets')->cascadeOnDelete();
                $table->foreignId('ledger_id')->constrained('loyalty_wallet_ledgers')->cascadeOnDelete();
                $table->unsignedBigInteger('points_used');
                $table->timestamps();

                $table->index(['tenant_id', 'customer_id']);
                $table->index(['tenant_id', 'ledger_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points_consumptions');
        Schema::dropIfExists('loyalty_points_buckets');
        Schema::dropIfExists('loyalty_wallet_ledgers');
        Schema::dropIfExists('loyalty_wallet_accounts');
    }
};
