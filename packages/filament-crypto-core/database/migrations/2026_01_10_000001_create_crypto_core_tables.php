<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crypto_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('code', 64);
            $table->string('name_fa');
            $table->string('type', 32);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
            $table->index(['tenant_id', 'type']);
        });

        Schema::create('crypto_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('ref_type', 64)->nullable();
            $table->string('ref_id', 128)->nullable();
            $table->timestamp('occurred_at');
            $table->string('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'occurred_at']);
            $table->index(['ref_type', 'ref_id']);
            $table->index(['tenant_id', 'updated_at']);
        });

        Schema::create('crypto_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ledger_id')->constrained('crypto_ledgers');
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('account_id')->constrained('crypto_accounts');
            $table->decimal('debit', 24, 8)->default(0);
            $table->decimal('credit', 24, 8)->default(0);
            $table->string('currency', 16);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['ledger_id', 'tenant_id']);
            $table->index(['tenant_id', 'account_id']);
            $table->index(['tenant_id', 'currency']);
        });

        Schema::create('crypto_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('mode', 32);
            $table->string('provider', 64)->nullable();
            $table->string('label');
            $table->string('currency', 16);
            $table->string('network', 32)->nullable();
            $table->string('status', 32)->default('active');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'updated_at']);
            $table->index(['tenant_id', 'currency', 'network']);
        });

        Schema::create('crypto_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('wallet_id')->constrained('crypto_wallets');
            $table->string('address');
            $table->string('tag_memo')->nullable();
            $table->string('derivation_path')->nullable();
            $table->string('status', 32)->default('active');
            $table->timestamp('last_seen_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['wallet_id', 'address', 'tag_memo']);
            $table->index(['tenant_id', 'status', 'updated_at']);
            $table->index(['tenant_id', 'wallet_id']);
        });

        Schema::create('crypto_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from', 16);
            $table->string('to', 16);
            $table->decimal('rate', 24, 8);
            $table->string('source', 64)->nullable();
            $table->timestamp('quoted_at');
            $table->timestamps();

            $table->index(['from', 'to', 'quoted_at']);
        });

        Schema::create('crypto_network_fees', function (Blueprint $table) {
            $table->id();
            $table->string('currency', 16);
            $table->string('network', 32);
            $table->string('fee_model', 32);
            $table->json('data')->nullable();
            $table->timestamp('quoted_at');
            $table->timestamps();

            $table->index(['currency', 'network', 'quoted_at']);
        });

        Schema::create('crypto_audit_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('event_type', 64);
            $table->string('subject_type', 128)->nullable();
            $table->string('subject_id', 128)->nullable();
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'event_type']);
            $table->index(['tenant_id', 'created_at']);
        });

        Schema::create('crypto_fee_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('plan_key', 32)->default('free');
            $table->decimal('invoice_percent', 8, 4)->default(0);
            $table->decimal('invoice_fixed', 16, 8)->default(0);
            $table->decimal('payout_fixed', 16, 8)->default(0);
            $table->decimal('conversion_percent', 8, 4)->default(0);
            $table->string('network_fee_mode', 32)->default('pass_through');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'plan_key']);
            $table->index(['tenant_id', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crypto_fee_policies');
        Schema::dropIfExists('crypto_audit_events');
        Schema::dropIfExists('crypto_network_fees');
        Schema::dropIfExists('crypto_rates');
        Schema::dropIfExists('crypto_addresses');
        Schema::dropIfExists('crypto_wallets');
        Schema::dropIfExists('crypto_ledger_entries');
        Schema::dropIfExists('crypto_ledgers');
        Schema::dropIfExists('crypto_accounts');
    }
};
