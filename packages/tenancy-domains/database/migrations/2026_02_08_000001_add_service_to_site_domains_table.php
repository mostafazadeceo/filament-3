<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('tenancy-domains.tables.site_domains', 'site_domains');

        if (! Schema::hasColumn($table, 'service')) {
            Schema::table($table, function (Blueprint $table): void {
                $table->string('service', 32)->default('all')->index();
            });
        }
    }

    public function down(): void
    {
        $table = config('tenancy-domains.tables.site_domains', 'site_domains');

        if (Schema::hasColumn($table, 'service')) {
            Schema::table($table, function (Blueprint $table): void {
                $table->dropIndex(['service']);
                $table->dropColumn('service');
            });
        }
    }
};
