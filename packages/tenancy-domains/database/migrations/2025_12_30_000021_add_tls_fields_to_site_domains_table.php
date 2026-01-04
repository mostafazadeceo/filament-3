<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('tenancy-domains.tables.site_domains', 'site_domains');

        Schema::table($table, function (Blueprint $table): void {
            $table->string('tls_status', 32)->default('not_requested')->index();
            $table->string('tls_provider', 100)->nullable()->index();
            $table->string('tls_mode', 20)->nullable();
            $table->timestamp('tls_requested_at')->nullable();
            $table->timestamp('tls_last_attempted_at')->nullable();
            $table->timestamp('tls_issued_at')->nullable();
            $table->timestamp('tls_expires_at')->nullable()->index();
            $table->text('tls_error')->nullable();
        });
    }

    public function down(): void
    {
        $table = config('tenancy-domains.tables.site_domains', 'site_domains');

        Schema::table($table, function (Blueprint $table): void {
            $table->dropIndex(['tls_status']);
            $table->dropIndex(['tls_provider']);
            $table->dropIndex(['tls_expires_at']);
            $table->dropColumn([
                'tls_status',
                'tls_provider',
                'tls_mode',
                'tls_requested_at',
                'tls_last_attempted_at',
                'tls_issued_at',
                'tls_expires_at',
                'tls_error',
            ]);
        });
    }
};
