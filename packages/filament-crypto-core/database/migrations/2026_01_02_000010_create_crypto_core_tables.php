<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('filament-crypto-core.tables', []);
        $accountsTable = $tables['accounts'] ?? 'crypto_accounts';
        $ledgersTable = $tables['ledgers'] ?? 'crypto_ledgers';
        $entriesTable = $tables['ledger_entries'] ?? 'crypto_ledger_entries';
        $walletsTable = $tables['wallets'] ?? 'crypto_wallets';
        $addressesTable = $tables['addresses'] ?? 'crypto_addresses';
        $ratesTable = $tables['rates'] ?? 'crypto_rates';
        $networkFeesTable = $tables['network_fees'] ?? 'crypto_network_fees';
        $auditLogsTable = $tables['audit_logs'] ?? 'crypto_audit_logs';

        if (! Schema::hasTable($accountsTable)) {
            Schema::create($accountsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('code');
                $table->string('name_fa');
                $table->string('type');
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'code']);
                $table->index(['tenant_id', 'type']);
            });
        }

        if (! Schema::hasTable($ledgersTable)) {
            Schema::create($ledgersTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('ref_type')->nullable();
                $table->string('ref_id')->nullable();
                $table->timestamp('occurred_at')->nullable();
                $table->string('description')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'ref_type', 'ref_id']);
                $table->index(['tenant_id', 'occurred_at']);
            });
        }

        if (! Schema::hasTable($entriesTable)) {
            Schema::create($entriesTable, function (Blueprint $table) use ($ledgersTable, $accountsTable): void {
                $table->id();
                $table->foreignId('ledger_id')->constrained($ledgersTable)->cascadeOnDelete();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('account_id')->constrained($accountsTable)->cascadeOnDelete();
                $table->decimal('debit', 20, 8)->default(0);
                $table->decimal('credit', 20, 8)->default(0);
                $table->string('currency', 16)->default('USDT');
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'account_id']);
                $table->index(['ledger_id', 'tenant_id']);
            });
        }

        if (! Schema::hasTable($walletsTable)) {
            Schema::create($walletsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('mode');
                $table->string('provider')->nullable();
                $table->string('label');
                $table->string('currency', 16)->default('USDT');
                $table->string('network', 64)->nullable();
                $table->string('status')->default('active');
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'currency', 'network']);
            });
        }

        if (! Schema::hasTable($addressesTable)) {
            Schema::create($addressesTable, function (Blueprint $table) use ($walletsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('wallet_id')->constrained($walletsTable)->cascadeOnDelete();
                $table->string('address', 255);
                $table->string('tag_memo', 120)->nullable();
                $table->string('derivation_path')->nullable();
                $table->string('status')->default('active');
                $table->timestamp('last_seen_at')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['wallet_id', 'address']);
                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($ratesTable)) {
            Schema::create($ratesTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
                $table->string('from_currency', 16);
                $table->string('to_currency', 16);
                $table->decimal('rate', 20, 8)->default(0);
                $table->string('source')->nullable();
                $table->timestamp('quoted_at')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'from_currency', 'to_currency']);
                $table->index(['from_currency', 'to_currency']);
            });
        }

        if (! Schema::hasTable($networkFeesTable)) {
            Schema::create($networkFeesTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
                $table->string('currency', 16);
                $table->string('network', 64);
                $table->string('fee_model', 64);
                $table->json('data')->nullable();
                $table->timestamp('quoted_at')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'currency', 'network']);
                $table->index(['currency', 'network']);
            });
        }

        if (! Schema::hasTable($auditLogsTable)) {
            Schema::create($auditLogsTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('action');
                $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('target_type')->nullable();
                $table->string('target_id')->nullable();
                $table->string('reason')->nullable();
                $table->json('payload')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'action']);
                $table->index(['tenant_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        $tables = config('filament-crypto-core.tables', []);
        $auditLogsTable = $tables['audit_logs'] ?? 'crypto_audit_logs';
        $networkFeesTable = $tables['network_fees'] ?? 'crypto_network_fees';
        $ratesTable = $tables['rates'] ?? 'crypto_rates';
        $addressesTable = $tables['addresses'] ?? 'crypto_addresses';
        $walletsTable = $tables['wallets'] ?? 'crypto_wallets';
        $entriesTable = $tables['ledger_entries'] ?? 'crypto_ledger_entries';
        $ledgersTable = $tables['ledgers'] ?? 'crypto_ledgers';
        $accountsTable = $tables['accounts'] ?? 'crypto_accounts';

        Schema::dropIfExists($auditLogsTable);
        Schema::dropIfExists($networkFeesTable);
        Schema::dropIfExists($ratesTable);
        Schema::dropIfExists($addressesTable);
        Schema::dropIfExists($walletsTable);
        Schema::dropIfExists($entriesTable);
        Schema::dropIfExists($ledgersTable);
        Schema::dropIfExists($accountsTable);
    }
};
