<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('webhooks')) {
            return;
        }

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE webhooks MODIFY secret TEXT');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE webhooks ALTER COLUMN secret TYPE TEXT');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('webhooks')) {
            return;
        }

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE webhooks MODIFY secret VARCHAR(255)');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE webhooks ALTER COLUMN secret TYPE VARCHAR(255)');
        }
    }
};
