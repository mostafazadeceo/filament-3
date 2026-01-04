<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('mailtrap-core.tables.sending_domains', 'mailtrap_sending_domains');
        $connections = config('mailtrap-core.tables.connections', 'mailtrap_connections');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) use ($connections) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('connection_id')->constrained($connections)->cascadeOnDelete();
            $table->unsignedBigInteger('domain_id');
            $table->string('domain_name');
            $table->boolean('dns_verified')->default(false);
            $table->timestamp('dns_verified_at')->nullable();
            $table->string('compliance_status')->nullable();
            $table->boolean('demo')->default(false);
            $table->json('dns_records')->nullable();
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['connection_id', 'domain_id'], 'mailtrap_domains_unique');
            $table->index(['tenant_id', 'dns_verified'], 'mailtrap_domains_verified_idx');
            $table->index('updated_at', 'mailtrap_domains_updated_idx');
        });
    }

    public function down(): void
    {
        $table = config('mailtrap-core.tables.sending_domains', 'mailtrap_sending_domains');
        Schema::dropIfExists($table);
    }
};
