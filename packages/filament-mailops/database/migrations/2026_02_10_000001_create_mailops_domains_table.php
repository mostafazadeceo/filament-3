<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('filament-mailops.tables.domains', 'mailops_domains');

        if (Schema::hasTable($tableName)) {
            return;
        }

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('status')->default('active');
            $table->string('dkim_selector')->default('dkim');
            $table->text('dkim_public_key')->nullable();
            $table->json('dns_snapshot')->nullable();
            $table->string('sync_status')->default('pending');
            $table->text('last_error')->nullable();
            $table->timestamp('mailu_synced_at')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'name']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'updated_at']);
        });
    }

    public function down(): void
    {
        $tableName = config('filament-mailops.tables.domains', 'mailops_domains');
        Schema::dropIfExists($tableName);
    }
};
