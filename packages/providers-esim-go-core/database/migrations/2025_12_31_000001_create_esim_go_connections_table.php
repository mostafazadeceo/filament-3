<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('providers-esim-go-core.tables.connections', 'esim_go_connections');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->text('api_key');
            $table->string('status')->default('active');
            $table->timestamp('last_tested_at')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'name'], 'esim_go_conn_unique');
            $table->index(['tenant_id', 'status'], 'esim_go_conn_status_idx');
            $table->index('updated_at', 'esim_go_conn_updated_idx');
        });
    }

    public function down(): void
    {
        $table = config('providers-esim-go-core.tables.connections', 'esim_go_connections');
        Schema::dropIfExists($table);
    }
};
