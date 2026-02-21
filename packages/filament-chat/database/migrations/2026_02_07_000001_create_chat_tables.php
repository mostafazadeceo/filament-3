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
        $userLinksTable = config('filament-chat.tables.user_links', 'chat_user_links');

        if (! Schema::hasTable($connectionsTable)) {
            Schema::create($connectionsTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('provider')->default('rocket_chat');
                $table->string('base_url');
                $table->string('status')->default('active');
                $table->string('auth_type')->default('admin_token');
                $table->string('api_user_id')->nullable();
                $table->text('api_token')->nullable();
                $table->string('oidc_issuer')->nullable();
                $table->string('oidc_client_id')->nullable();
                $table->text('oidc_client_secret')->nullable();
                $table->string('oidc_scopes')->nullable();
                $table->json('settings')->nullable();
                $table->timestamp('last_tested_at')->nullable();
                $table->timestamp('last_sync_at')->nullable();
                $table->text('last_error_message')->nullable();
                $table->timestamp('last_error_at')->nullable();
                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->unsignedBigInteger('updated_by_user_id')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'name'], 'chat_connections_tenant_name_unique');
                $table->index(['tenant_id', 'status'], 'chat_connections_status_idx');
                $table->index(['tenant_id', 'provider'], 'chat_connections_provider_idx');
                $table->index(['tenant_id', 'base_url'], 'chat_connections_url_idx');
            });
        }

        if (! Schema::hasTable($userLinksTable)) {
            Schema::create($userLinksTable, function (Blueprint $table) use ($connectionsTable) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('chat_connection_id')->constrained($connectionsTable)->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('chat_user_id')->nullable();
                $table->string('username')->nullable();
                $table->string('status')->default('active');
                $table->timestamp('synced_at')->nullable();
                $table->text('last_error_message')->nullable();
                $table->timestamp('last_error_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->unique(['chat_connection_id', 'user_id'], 'chat_user_links_unique');
                $table->index(['tenant_id', 'status'], 'chat_user_links_status_idx');
                $table->index(['chat_connection_id', 'username'], 'chat_user_links_username_idx');
            });
        }
    }

    public function down(): void
    {
        $connectionsTable = config('filament-chat.tables.connections', 'chat_connections');
        $userLinksTable = config('filament-chat.tables.user_links', 'chat_user_links');

        Schema::dropIfExists($userLinksTable);
        Schema::dropIfExists($connectionsTable);
    }
};
