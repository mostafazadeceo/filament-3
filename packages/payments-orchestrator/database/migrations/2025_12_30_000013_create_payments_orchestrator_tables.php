<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('payments-orchestrator.tables', []);
        $connectionsTable = $tables['gateway_connections'] ?? 'payment_gateway_connections';
        $intentsTable = $tables['payment_intents'] ?? 'payment_intents';
        $webhooksTable = $tables['webhook_events'] ?? 'payment_webhook_events';

        $ordersTable = config('commerce-orders.tables.orders', 'commerce_orders');

        if (! Schema::hasTable($connectionsTable)) {
            Schema::create($connectionsTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('provider_key');
                $table->string('name');
                $table->string('environment')->default('sandbox');
                $table->text('api_key')->nullable();
                $table->text('api_secret')->nullable();
                $table->text('webhook_secret')->nullable();
                $table->json('settings')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['tenant_id', 'provider_key', 'environment'], 'pay_gateway_conn_unique');
                $table->index(['tenant_id', 'provider_key', 'is_active'], 'pay_gateway_conn_active_idx');
            });
        }
        if (Schema::hasTable($connectionsTable)) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                $uniqueIndex = 'pay_gateway_conn_unique';
                $activeIndex = 'pay_gateway_conn_active_idx';

                $uniqueExists = DB::select("SHOW INDEX FROM {$connectionsTable} WHERE Key_name = ?", [$uniqueIndex]);
                if (empty($uniqueExists)) {
                    Schema::table($connectionsTable, function (Blueprint $table) use ($uniqueIndex) {
                        $table->unique(['tenant_id', 'provider_key', 'environment'], $uniqueIndex);
                    });
                }

                $activeExists = DB::select("SHOW INDEX FROM {$connectionsTable} WHERE Key_name = ?", [$activeIndex]);
                if (empty($activeExists)) {
                    Schema::table($connectionsTable, function (Blueprint $table) use ($activeIndex) {
                        $table->index(['tenant_id', 'provider_key', 'is_active'], $activeIndex);
                    });
                }
            }
        }

        if (! Schema::hasTable($intentsTable)) {
            Schema::create($intentsTable, function (Blueprint $table) use ($ordersTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
                $table->foreignId('order_id')->constrained($ordersTable)->cascadeOnDelete();
                $table->string('provider_key');
                $table->string('status')->default('pending');
                $table->string('currency', 8)->default('IRR');
                $table->decimal('amount', 18, 4)->default(0);
                $table->string('idempotency_key')->nullable();
                $table->string('provider_reference')->nullable();
                $table->string('redirect_url')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'idempotency_key'], 'pay_intents_tenant_idem_uq');
                $table->index(['tenant_id', 'order_id'], 'pay_intents_tenant_order_idx');
                $table->index(['tenant_id', 'provider_key', 'status'], 'pay_intents_provider_status_idx');
            });
        }
        if (Schema::hasTable($intentsTable)) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                $uniqueIndex = 'pay_intents_tenant_idem_uq';
                $orderIndex = 'pay_intents_tenant_order_idx';
                $statusIndex = 'pay_intents_provider_status_idx';

                $uniqueExists = DB::select("SHOW INDEX FROM {$intentsTable} WHERE Key_name = ?", [$uniqueIndex]);
                if (empty($uniqueExists)) {
                    Schema::table($intentsTable, function (Blueprint $table) use ($uniqueIndex) {
                        $table->unique(['tenant_id', 'idempotency_key'], $uniqueIndex);
                    });
                }

                $orderExists = DB::select("SHOW INDEX FROM {$intentsTable} WHERE Key_name = ?", [$orderIndex]);
                if (empty($orderExists)) {
                    Schema::table($intentsTable, function (Blueprint $table) use ($orderIndex) {
                        $table->index(['tenant_id', 'order_id'], $orderIndex);
                    });
                }

                $statusExists = DB::select("SHOW INDEX FROM {$intentsTable} WHERE Key_name = ?", [$statusIndex]);
                if (empty($statusExists)) {
                    Schema::table($intentsTable, function (Blueprint $table) use ($statusIndex) {
                        $table->index(['tenant_id', 'provider_key', 'status'], $statusIndex);
                    });
                }
            }
        }

        if (! Schema::hasTable($webhooksTable)) {
            Schema::create($webhooksTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('provider_key');
                $table->string('event_id');
                $table->string('signature')->nullable();
                $table->longText('payload');
                $table->json('headers')->nullable();
                $table->string('status')->default('received');
                $table->string('idempotency_key')->nullable();
                $table->timestamp('received_at')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'provider_key', 'event_id'], 'pay_webhook_event_unique');
                $table->index(['tenant_id', 'provider_key', 'status'], 'pay_webhook_status_idx');
            });
        }
        if (Schema::hasTable($webhooksTable)) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                $uniqueIndex = 'pay_webhook_event_unique';
                $statusIndex = 'pay_webhook_status_idx';

                $uniqueExists = DB::select("SHOW INDEX FROM {$webhooksTable} WHERE Key_name = ?", [$uniqueIndex]);
                if (empty($uniqueExists)) {
                    Schema::table($webhooksTable, function (Blueprint $table) use ($uniqueIndex) {
                        $table->unique(['tenant_id', 'provider_key', 'event_id'], $uniqueIndex);
                    });
                }

                $statusExists = DB::select("SHOW INDEX FROM {$webhooksTable} WHERE Key_name = ?", [$statusIndex]);
                if (empty($statusExists)) {
                    Schema::table($webhooksTable, function (Blueprint $table) use ($statusIndex) {
                        $table->index(['tenant_id', 'provider_key', 'status'], $statusIndex);
                    });
                }
            }
        }
    }

    public function down(): void
    {
        $tables = config('payments-orchestrator.tables', []);
        $webhooksTable = $tables['webhook_events'] ?? 'payment_webhook_events';
        $intentsTable = $tables['payment_intents'] ?? 'payment_intents';
        $connectionsTable = $tables['gateway_connections'] ?? 'payment_gateway_connections';

        Schema::dropIfExists($webhooksTable);
        Schema::dropIfExists($intentsTable);
        Schema::dropIfExists($connectionsTable);
    }
};
