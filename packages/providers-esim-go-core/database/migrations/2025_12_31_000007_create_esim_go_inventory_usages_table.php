<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('providers-esim-go-core.tables.inventory_usages', 'esim_go_inventory_usages');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('usage_id');
            $table->string('bundle_name')->nullable();
            $table->decimal('remaining', 18, 4)->nullable();
            $table->timestamp('expiry_at')->nullable();
            $table->json('countries')->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'usage_id'], 'esim_go_usage_idx');
            $table->index('updated_at', 'esim_go_usage_updated_idx');
        });
    }

    public function down(): void
    {
        $table = config('providers-esim-go-core.tables.inventory_usages', 'esim_go_inventory_usages');
        Schema::dropIfExists($table);
    }
};
