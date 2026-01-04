<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('providers-esim-go-core.tables.esims', 'esim_go_esims');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('iccid')->index('esim_go_esims_iccid_idx');
            $table->string('matching_id')->nullable();
            $table->string('smdp_address')->nullable();
            $table->string('state')->nullable();
            $table->timestamp('first_installed_at')->nullable();
            $table->timestamp('last_refreshed_at')->nullable();
            $table->string('external_ref')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'order_id'], 'esim_go_esims_order_idx');
        });
    }

    public function down(): void
    {
        $table = config('providers-esim-go-core.tables.esims', 'esim_go_esims');
        Schema::dropIfExists($table);
    }
};
