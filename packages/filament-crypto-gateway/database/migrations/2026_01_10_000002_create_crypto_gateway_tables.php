<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crypto_provider_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('provider', 64);
            $table->string('env', 16)->default('prod');
            $table->string('merchant_id', 128)->nullable();
            $table->text('api_key_encrypted')->nullable();
            $table->text('secret_encrypted')->nullable();
            $table->json('config_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'provider', 'env']);
            $table->index(['tenant_id', 'provider']);
        });

        Schema::create('crypto_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('provider', 64);
            $table->string('order_id', 128);
            $table->string('external_uuid', 128)->nullable();
            $table->decimal('amount', 24, 8);
            $table->string('currency', 16);
            $table->string('to_currency', 16)->nullable();
            $table->string('network', 32)->nullable();
            $table->string('address')->nullable();
            $table->string('status', 32)->default('draft');
            $table->boolean('is_final')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->decimal('tolerance_percent', 8, 4)->default(0);
            $table->decimal('subtract_percent', 8, 4)->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'provider', 'order_id']);
            $table->index(['tenant_id', 'status', 'updated_at']);
            $table->index(['provider', 'external_uuid']);
        });

        Schema::create('crypto_invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('invoice_id')->constrained('crypto_invoices');
            $table->string('txid', 128)->nullable();
            $table->string('from_address')->nullable();
            $table->decimal('payer_amount', 24, 8)->default(0);
            $table->string('payer_currency', 16)->nullable();
            $table->unsignedInteger('confirmations')->default(0);
            $table->string('status', 32)->default('seen');
            $table->json('raw_payload_json')->nullable();
            $table->timestamp('seen_at')->nullable();
            $table->timestamps();

            $table->index(['invoice_id', 'txid']);
            $table->index(['tenant_id', 'status', 'updated_at']);
        });

        Schema::create('crypto_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('provider', 64);
            $table->string('order_id', 128);
            $table->string('external_uuid', 128)->nullable();
            $table->string('to_address');
            $table->decimal('amount', 24, 8);
            $table->string('currency', 16);
            $table->string('network', 32)->nullable();
            $table->decimal('fee', 24, 8)->default(0);
            $table->string('status', 32)->default('draft');
            $table->boolean('is_final')->default(false);
            $table->string('fail_reason', 128)->nullable();
            $table->string('txid', 128)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'provider', 'order_id']);
            $table->index(['tenant_id', 'status', 'updated_at']);
            $table->index(['provider', 'external_uuid']);
        });

        Schema::create('crypto_webhook_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants');
            $table->string('provider', 64);
            $table->string('event_id', 128)->nullable();
            $table->boolean('signature_ok')->default(false);
            $table->boolean('ip_ok')->default(false);
            $table->string('idempotency_key', 190);
            $table->string('payload_hash', 64);
            $table->json('headers_json')->nullable();
            $table->string('remote_ip', 64)->nullable();
            $table->longText('raw_payload')->nullable();
            $table->json('payload_json')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('status', 32)->default('received');
            $table->text('error')->nullable();
            $table->unsignedInteger('retry_count')->default(0);
            $table->timestamps();

            $table->unique(['provider', 'idempotency_key']);
            $table->index(['provider', 'event_id']);
            $table->index(['tenant_id', 'status', 'updated_at']);
            $table->index(['payload_hash']);
        });

        Schema::create('crypto_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('scope', 64);
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->json('result_json')->nullable();
            $table->string('status', 32)->default('pending');
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'started_at']);
        });

        Schema::create('crypto_ai_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('period', 16)->default('daily');
            $table->timestamp('report_at');
            $table->text('summary_md')->nullable();
            $table->json('payload_json')->nullable();
            $table->json('anomalies_json')->nullable();
            $table->string('status', 32)->default('ready');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'report_at']);
            $table->index(['tenant_id', 'status', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crypto_ai_reports');
        Schema::dropIfExists('crypto_reconciliations');
        Schema::dropIfExists('crypto_webhook_calls');
        Schema::dropIfExists('crypto_payouts');
        Schema::dropIfExists('crypto_invoice_payments');
        Schema::dropIfExists('crypto_invoices');
        Schema::dropIfExists('crypto_provider_accounts');
    }
};
