<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('mailtrap-core.tables.connections', 'mailtrap_connections');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->text('api_token');
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('default_inbox_id')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('last_tested_at')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'name'], 'mailtrap_conn_unique');
            $table->index(['tenant_id', 'status'], 'mailtrap_conn_status_idx');
            $table->index('account_id', 'mailtrap_conn_account_idx');
            $table->index('updated_at', 'mailtrap_conn_updated_idx');
        });
    }

    public function down(): void
    {
        $table = config('mailtrap-core.tables.connections', 'mailtrap_connections');
        Schema::dropIfExists($table);
    }
};
