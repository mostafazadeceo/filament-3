<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('providers-esim-go-core.tables.products', 'esim_go_products');

        if (! Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) {
            if (! Schema::hasColumn($table->getTable(), 'countries_meta')) {
                $table->json('countries_meta')->nullable()->after('countries');
            }
        });
    }

    public function down(): void
    {
        $table = config('providers-esim-go-core.tables.products', 'esim_go_products');

        if (! Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) {
            if (Schema::hasColumn($table->getTable(), 'countries_meta')) {
                $table->dropColumn('countries_meta');
            }
        });
    }
};
