<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(config('mailtrap-core.tables.connections', 'mailtrap_connections'), function (Blueprint $table): void {
            if (! Schema::hasColumn($table->getTable(), 'send_api_token')) {
                $table->text('send_api_token')->nullable()->after('api_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table(config('mailtrap-core.tables.connections', 'mailtrap_connections'), function (Blueprint $table): void {
            if (Schema::hasColumn($table->getTable(), 'send_api_token')) {
                $table->dropColumn('send_api_token');
            }
        });
    }
};
