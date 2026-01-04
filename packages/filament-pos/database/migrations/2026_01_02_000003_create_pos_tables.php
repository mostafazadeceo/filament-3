<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('filament-pos.tables', []);
        $storesTable = $tables['stores'] ?? 'pos_stores';
        $registersTable = $tables['registers'] ?? 'pos_registers';
        $devicesTable = $tables['devices'] ?? 'pos_devices';
        $sessionsTable = $tables['cashier_sessions'] ?? 'pos_cashier_sessions';
        $movementsTable = $tables['cash_movements'] ?? 'pos_cash_movements';
        $salesTable = $tables['sales'] ?? 'pos_sales';
        $saleItemsTable = $tables['sale_items'] ?? 'pos_sale_items';
        $salePaymentsTable = $tables['sale_payments'] ?? 'pos_sale_payments';
        $syncTable = $tables['sync_cursors'] ?? 'pos_sync_cursors';
        $outboxTable = $tables['outbox'] ?? 'pos_outbox';

        if (! Schema::hasTable($storesTable)) {
            Schema::create($storesTable, function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('code')->nullable();
                $table->string('status')->default('active');
                $table->string('currency', 8)->default('IRR');
                $table->string('timezone')->nullable();
                $table->text('address')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'code']);
            });
        }

        if (! Schema::hasTable($registersTable)) {
            Schema::create($registersTable, function (Blueprint $table) use ($storesTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('store_id')->constrained($storesTable)->cascadeOnDelete();
                $table->string('name');
                $table->string('code')->nullable();
                $table->string('status')->default('active');
                $table->timestamp('last_opened_at')->nullable();
                $table->timestamp('last_closed_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'store_id']);
                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($devicesTable)) {
            Schema::create($devicesTable, function (Blueprint $table) use ($registersTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('register_id')->constrained($registersTable)->cascadeOnDelete();
                $table->string('device_uid');
                $table->string('status')->default('active');
                $table->timestamp('last_seen_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'device_uid']);
                $table->index(['tenant_id', 'register_id']);
            });
        }

        if (! Schema::hasTable($sessionsTable)) {
            Schema::create($sessionsTable, function (Blueprint $table) use ($storesTable, $registersTable, $devicesTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('store_id')->constrained($storesTable)->cascadeOnDelete();
                $table->foreignId('register_id')->constrained($registersTable)->cascadeOnDelete();
                $table->foreignId('device_id')->nullable()->constrained($devicesTable)->nullOnDelete();
                $table->foreignId('opened_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('closed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status')->default('open');
                $table->timestamp('opened_at')->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->decimal('opening_float', 18, 4)->default(0);
                $table->decimal('closing_cash', 18, 4)->default(0);
                $table->decimal('expected_cash', 18, 4)->default(0);
                $table->decimal('variance', 18, 4)->default(0);
                $table->text('notes')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'register_id']);
                $table->index(['tenant_id', 'status']);
                $table->index(['opened_at']);
            });
        }

        if (! Schema::hasTable($movementsTable)) {
            Schema::create($movementsTable, function (Blueprint $table) use ($sessionsTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('session_id')->constrained($sessionsTable)->cascadeOnDelete();
                $table->string('type');
                $table->decimal('amount', 18, 4)->default(0);
                $table->string('reason')->nullable();
                $table->timestamp('recorded_at')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'session_id']);
                $table->index(['tenant_id', 'type']);
            });
        }

        if (! Schema::hasTable($salesTable)) {
            Schema::create($salesTable, function (Blueprint $table) use ($storesTable, $registersTable, $sessionsTable, $devicesTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('store_id')->constrained($storesTable)->cascadeOnDelete();
                $table->foreignId('register_id')->constrained($registersTable)->cascadeOnDelete();
                $table->foreignId('session_id')->nullable()->constrained($sessionsTable)->nullOnDelete();
                $table->foreignId('device_id')->nullable()->constrained($devicesTable)->nullOnDelete();
                $table->string('receipt_no')->nullable();
                $table->string('status')->default('open');
                $table->string('payment_status')->default('pending');
                $table->string('currency', 8)->default('IRR');
                $table->decimal('subtotal', 18, 4)->default(0);
                $table->decimal('discount_total', 18, 4)->default(0);
                $table->decimal('tax_total', 18, 4)->default(0);
                $table->decimal('total', 18, 4)->default(0);
                $table->string('source')->default('pos');
                $table->string('idempotency_key')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('completed_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'idempotency_key']);
                $table->index(['tenant_id', 'store_id']);
                $table->index(['tenant_id', 'register_id']);
                $table->index(['tenant_id', 'status']);
            });
        }

        if (! Schema::hasTable($saleItemsTable)) {
            Schema::create($saleItemsTable, function (Blueprint $table) use ($salesTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('sale_id')->constrained($salesTable)->cascadeOnDelete();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->unsignedBigInteger('variant_id')->nullable();
                $table->string('name');
                $table->string('sku')->nullable();
                $table->string('barcode')->nullable();
                $table->decimal('quantity', 12, 4)->default(1);
                $table->decimal('unit_price', 18, 4)->default(0);
                $table->decimal('discount_amount', 18, 4)->default(0);
                $table->decimal('tax_amount', 18, 4)->default(0);
                $table->decimal('total', 18, 4)->default(0);
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'sale_id']);
            });
        }

        if (! Schema::hasTable($salePaymentsTable)) {
            Schema::create($salePaymentsTable, function (Blueprint $table) use ($salesTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('sale_id')->constrained($salesTable)->cascadeOnDelete();
                $table->string('provider')->default('manual');
                $table->decimal('amount', 18, 4)->default(0);
                $table->string('currency', 8)->default('IRR');
                $table->string('status')->default('pending');
                $table->string('reference')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'sale_id']);
                $table->index(['tenant_id', 'provider']);
            });
        }

        if (! Schema::hasTable($syncTable)) {
            Schema::create($syncTable, function (Blueprint $table) use ($devicesTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('device_id')->constrained($devicesTable)->cascadeOnDelete();
                $table->string('cursor')->nullable();
                $table->timestamp('last_synced_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'device_id']);
            });
        }

        if (! Schema::hasTable($outboxTable)) {
            Schema::create($outboxTable, function (Blueprint $table) use ($devicesTable): void {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('device_id')->nullable()->constrained($devicesTable)->nullOnDelete();
                $table->string('event_type');
                $table->string('event_id')->nullable();
                $table->string('idempotency_key')->nullable();
                $table->string('status')->default('pending');
                $table->string('error_reason')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->json('payload');
                $table->timestamps();

                $table->unique(['tenant_id', 'idempotency_key']);
                $table->index(['tenant_id', 'event_type']);
                $table->index(['tenant_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        $tables = config('filament-pos.tables', []);
        $outboxTable = $tables['outbox'] ?? 'pos_outbox';
        $syncTable = $tables['sync_cursors'] ?? 'pos_sync_cursors';
        $salePaymentsTable = $tables['sale_payments'] ?? 'pos_sale_payments';
        $saleItemsTable = $tables['sale_items'] ?? 'pos_sale_items';
        $salesTable = $tables['sales'] ?? 'pos_sales';
        $movementsTable = $tables['cash_movements'] ?? 'pos_cash_movements';
        $sessionsTable = $tables['cashier_sessions'] ?? 'pos_cashier_sessions';
        $devicesTable = $tables['devices'] ?? 'pos_devices';
        $registersTable = $tables['registers'] ?? 'pos_registers';
        $storesTable = $tables['stores'] ?? 'pos_stores';

        Schema::dropIfExists($outboxTable);
        Schema::dropIfExists($syncTable);
        Schema::dropIfExists($salePaymentsTable);
        Schema::dropIfExists($saleItemsTable);
        Schema::dropIfExists($salesTable);
        Schema::dropIfExists($movementsTable);
        Schema::dropIfExists($sessionsTable);
        Schema::dropIfExists($devicesTable);
        Schema::dropIfExists($registersTable);
        Schema::dropIfExists($storesTable);
    }
};
