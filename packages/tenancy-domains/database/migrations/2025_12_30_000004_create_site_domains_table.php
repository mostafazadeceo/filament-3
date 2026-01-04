<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('tenancy-domains.tables', []);
        $domainsTable = $tables['site_domains'] ?? 'site_domains';

        if (Schema::hasTable($domainsTable)) {
            return;
        }

        Schema::create($domainsTable, function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedBigInteger('site_id')->nullable();
            $table->string('host')->unique();
            $table->string('type')->default('custom');
            $table->string('status')->default('pending');
            $table->string('verification_method')->nullable();
            $table->string('dns_token')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'site_id']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        $tables = config('tenancy-domains.tables', []);
        $domainsTable = $tables['site_domains'] ?? 'site_domains';

        Schema::dropIfExists($domainsTable);
    }
};
