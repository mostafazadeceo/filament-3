<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE relograde_brand_options MODIFY redeem_value VARCHAR(191) NULL');
            DB::statement('ALTER TABLE relograde_products MODIFY redeem_value VARCHAR(191) NULL');

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE relograde_brand_options ALTER COLUMN redeem_value TYPE VARCHAR(191)');
            DB::statement('ALTER TABLE relograde_products ALTER COLUMN redeem_value TYPE VARCHAR(191)');
        }
    }

    public function down(): void
    {
        // برگرداندن نوع ستون‌ها به عددی ممکن است داده‌ها را خراب کند.
    }
};
