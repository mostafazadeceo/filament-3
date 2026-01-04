<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('providers-esim-go-core.tables.orders', 'esim_go_orders');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedBigInteger('commerce_order_id')->nullable();
            $table->unsignedBigInteger('connection_id')->nullable();
            $table->string('provider_reference')->nullable();
            $table->string('status')->default('pending');
            $table->text('status_message')->nullable();
            $table->decimal('total', 18, 4)->default(0);
            $table->string('currency', 8)->default('USD');
            $table->json('raw_request')->nullable();
            $table->json('raw_response')->nullable();
            $table->string('correlation_id')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status'], 'esim_go_orders_status_idx');
            $table->index(['tenant_id', 'provider_reference'], 'esim_go_orders_ref_idx');
            $table->index('updated_at', 'esim_go_orders_updated_idx');
        });
    }

    public function down(): void
    {
        $table = config('providers-esim-go-core.tables.orders', 'esim_go_orders');
        Schema::dropIfExists($table);
    }
};
