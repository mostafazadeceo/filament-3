<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('filament-crypto-gateway.tables', []);
        $providerAccountsTable = $tables['provider_accounts'] ?? 'crypto_provider_accounts';
        $invoicesTable = $tables['invoices'] ?? 'crypto_invoices';
        $invoicePaymentsTable = $tables['invoice_payments'] ?? 'crypto_invoice_payments';
        $payoutsTable = $tables['payouts'] ?? 'crypto_payouts';
        $webhookCallsTable = $tables['webhook_calls'] ?? 'crypto_webhook_calls';
        $reconciliationsTable = $tables['reconciliations'] ?? 'crypto_reconciliations';
        $aiReportsTable = $tables['ai_reports'] ?? 'crypto_ai_reports';

        if (! Schema::hasTable($providerAccountsTable)) {
            Schema::create($providerAccountsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('provider');
                $table->string('env')->default('sandbox');
                $table->string('merchant_id')->nullable();
                $table->text('api_key_encrypted')->nullable();
                $table->text('secret_encrypted')->nullable();
                $table->json('config_json')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['tenant_id', 'provider']);
                $table->index(['tenant_id', 'is_active']);
            });
        }

        if (! Schema::hasTable($invoicesTable)) {
            Schema::create($invoicesTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('provider');
                $table->string('order_id');
                $table->string('external_uuid')->nullable();
                $table->decimal('amount', 20, 8)->default(0);
                $table->string('currency', 16)->default('USDT');
                $table->string('to_currency', 16)->nullable();
                $table->string('network', 64)->nullable();
                $table->string('address', 255)->nullable();
                $table->string('status')->default('draft');
                $table->boolean('is_final')->default(false);
                $table->timestamp('expires_at')->nullable();
                $table->decimal('tolerance_percent', 8, 4)->nullable();
                $table->decimal('subtract_percent', 8, 4)->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'provider', 'order_id']);
                $table->index(['tenant_id', 'status', 'updated_at']);
                $table->index(['tenant_id', 'provider']);
            });
        }

        if (! Schema::hasTable($invoicePaymentsTable)) {
            Schema::create($invoicePaymentsTable, function (Blueprint $table) use ($invoicesTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('invoice_id')->constrained($invoicesTable)->cascadeOnDelete();
                $table->string('txid')->nullable();
                $table->string('from_address')->nullable();
                $table->decimal('payer_amount', 20, 8)->nullable();
                $table->string('payer_currency', 16)->nullable();
                $table->unsignedInteger('confirmations')->default(0);
                $table->string('status')->default('seen');
                $table->json('raw_payload_json')->nullable();
                $table->timestamp('seen_at')->nullable();
                $table->timestamps();

                $table->index(['invoice_id', 'txid']);
                $table->index(['tenant_id', 'invoice_id']);
            });
        }

        if (! Schema::hasTable($payoutsTable)) {
            Schema::create($payoutsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('provider');
                $table->string('order_id');
                $table->string('external_uuid')->nullable();
                $table->string('to_address');
                $table->decimal('amount', 20, 8)->default(0);
                $table->string('currency', 16)->default('USDT');
                $table->string('network', 64)->nullable();
                $table->decimal('fee', 20, 8)->nullable();
                $table->string('status')->default('draft');
                $table->boolean('is_final')->default(false);
                $table->string('fail_reason')->nullable();
                $table->string('txid')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'provider', 'order_id']);
                $table->index(['tenant_id', 'status', 'updated_at']);
            });
        }

        if (! Schema::hasTable($webhookCallsTable)) {
            Schema::create($webhookCallsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
                $table->string('provider');
                $table->string('event_id')->nullable();
                $table->boolean('signature_ok')->default(false);
                $table->boolean('ip_ok')->default(false);
                $table->string('idempotency_key')->nullable();
                $table->string('payload_hash')->nullable();
                $table->json('payload_json')->nullable();
                $table->timestamp('received_at')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->string('status')->default('received');
                $table->text('error')->nullable();
                $table->timestamps();

                $table->index(['provider', 'event_id']);
                $table->index(['provider', 'status']);
                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($reconciliationsTable)) {
            Schema::create($reconciliationsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('scope');
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->json('result_json')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();

                $table->index(['tenant_id', 'scope']);
                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($aiReportsTable)) {
            Schema::create($aiReportsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->timestamp('period_start')->nullable();
                $table->timestamp('period_end')->nullable();
                $table->string('provider')->nullable();
                $table->text('summary_text')->nullable();
                $table->json('anomalies_json')->nullable();
                $table->json('meta_json')->nullable();
                $table->timestamp('generated_at')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'period_start', 'period_end']);
            });
        }
    }

    public function down(): void
    {
        $tables = config('filament-crypto-gateway.tables', []);
        $aiReportsTable = $tables['ai_reports'] ?? 'crypto_ai_reports';
        $reconciliationsTable = $tables['reconciliations'] ?? 'crypto_reconciliations';
        $webhookCallsTable = $tables['webhook_calls'] ?? 'crypto_webhook_calls';
        $payoutsTable = $tables['payouts'] ?? 'crypto_payouts';
        $invoicePaymentsTable = $tables['invoice_payments'] ?? 'crypto_invoice_payments';
        $invoicesTable = $tables['invoices'] ?? 'crypto_invoices';
        $providerAccountsTable = $tables['provider_accounts'] ?? 'crypto_provider_accounts';

        Schema::dropIfExists($aiReportsTable);
        Schema::dropIfExists($reconciliationsTable);
        Schema::dropIfExists($webhookCallsTable);
        Schema::dropIfExists($payoutsTable);
        Schema::dropIfExists($invoicePaymentsTable);
        Schema::dropIfExists($invoicesTable);
        Schema::dropIfExists($providerAccountsTable);
    }
};
