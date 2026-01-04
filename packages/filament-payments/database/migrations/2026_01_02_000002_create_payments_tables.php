<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('filament-payments.tables', []);
        $intentsTable = $tables['payment_intents'] ?? 'payments_payment_intents';
        $attemptsTable = $tables['payment_attempts'] ?? 'payments_payment_attempts';
        $connectionsTable = $tables['provider_connections'] ?? 'payments_provider_connections';
        $webhooksTable = $tables['webhook_events'] ?? 'payments_webhook_events';
        $refundsTable = $tables['refunds'] ?? 'payments_refunds';
        $reconciliationsTable = $tables['reconciliations'] ?? 'payments_reconciliations';

        if (! Schema::hasTable($connectionsTable)) {
            Schema::create($connectionsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('provider_key');
                $table->string('display_name')->nullable();
                $table->json('credentials')->nullable();
                $table->boolean('is_active')->default(true);
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'provider_key']);
                $table->index(['tenant_id', 'is_active']);
            });
        }

        if (! Schema::hasTable($intentsTable)) {
            Schema::create($intentsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('status')->default('pending');
                $table->string('provider')->nullable();
                $table->string('provider_reference')->nullable();
                $table->string('currency', 8)->default('IRR');
                $table->decimal('amount', 18, 4)->default(0);
                $table->string('idempotency_key')->nullable();
                $table->string('redirect_url')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamp('failed_at')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'idempotency_key']);
                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'provider']);
                $table->index(['tenant_id', 'reference_type', 'reference_id'], 'pay_intent_tenant_ref_idx');
                $table->index('updated_at');
            });
        }

        if (! Schema::hasTable($attemptsTable)) {
            Schema::create($attemptsTable, function (Blueprint $table) use ($intentsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('payment_intent_id')->constrained($intentsTable)->cascadeOnDelete();
                $table->string('status')->default('pending');
                $table->string('provider')->nullable();
                $table->string('provider_reference')->nullable();
                $table->json('payload')->nullable();
                $table->json('response')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'payment_intent_id']);
                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($webhooksTable)) {
            Schema::create($webhooksTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
                $table->string('provider');
                $table->string('event_type')->nullable();
                $table->string('external_id')->nullable();
                $table->boolean('signature_valid')->default(false);
                $table->string('status')->default('received');
                $table->json('headers')->nullable();
                $table->json('payload')->nullable();
                $table->timestamp('received_at')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();

                $table->unique(['provider', 'external_id']);
                $table->index(['provider', 'status']);
            });
        }

        if (! Schema::hasTable($refundsTable)) {
            Schema::create($refundsTable, function (Blueprint $table) use ($intentsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('payment_intent_id')->constrained($intentsTable)->cascadeOnDelete();
                $table->string('status')->default('pending');
                $table->string('currency', 8)->default('IRR');
                $table->decimal('amount', 18, 4)->default(0);
                $table->string('provider')->nullable();
                $table->string('reference')->nullable();
                $table->string('idempotency_key')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'idempotency_key']);
                $table->index(['tenant_id', 'payment_intent_id']);
                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($reconciliationsTable)) {
            Schema::create($reconciliationsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('provider')->nullable();
                $table->timestamp('period_start')->nullable();
                $table->timestamp('period_end')->nullable();
                $table->string('status')->default('pending');
                $table->json('summary')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'provider']);
                $table->index(['tenant_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        $tables = config('filament-payments.tables', []);
        $reconciliationsTable = $tables['reconciliations'] ?? 'payments_reconciliations';
        $refundsTable = $tables['refunds'] ?? 'payments_refunds';
        $webhooksTable = $tables['webhook_events'] ?? 'payments_webhook_events';
        $attemptsTable = $tables['payment_attempts'] ?? 'payments_payment_attempts';
        $intentsTable = $tables['payment_intents'] ?? 'payments_payment_intents';
        $connectionsTable = $tables['provider_connections'] ?? 'payments_provider_connections';

        Schema::dropIfExists($reconciliationsTable);
        Schema::dropIfExists($refundsTable);
        Schema::dropIfExists($webhooksTable);
        Schema::dropIfExists($attemptsTable);
        Schema::dropIfExists($intentsTable);
        Schema::dropIfExists($connectionsTable);
    }
};
