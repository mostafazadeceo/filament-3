<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connectionsTable = config('filament-chat.tables.connections', 'chat_connections');

        if (! Schema::hasTable($connectionsTable)) {
            return;
        }

        $schema = Schema::getConnection()->getSchemaBuilder();
        if (method_exists($schema, 'hasIndex') && $schema->hasIndex($connectionsTable, 'chat_connections_oidc_client_unique')) {
            return;
        }

        Schema::table($connectionsTable, function (Blueprint $table): void {
            $table->unique(['oidc_client_id'], 'chat_connections_oidc_client_unique');
        });
    }

    public function down(): void
    {
        $connectionsTable = config('filament-chat.tables.connections', 'chat_connections');

        if (! Schema::hasTable($connectionsTable)) {
            return;
        }

        Schema::table($connectionsTable, function (Blueprint $table): void {
            $table->dropUnique('chat_connections_oidc_client_unique');
        });
    }

    // no-op
};
