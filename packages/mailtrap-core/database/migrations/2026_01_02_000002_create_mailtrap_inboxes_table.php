<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('mailtrap-core.tables.inboxes', 'mailtrap_inboxes');
        $connections = config('mailtrap-core.tables.connections', 'mailtrap_connections');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) use ($connections) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('connection_id')->constrained($connections)->cascadeOnDelete();
            $table->unsignedBigInteger('inbox_id');
            $table->string('name');
            $table->string('status')->nullable();
            $table->string('username')->nullable();
            $table->string('email_domain')->nullable();
            $table->string('api_domain')->nullable();
            $table->json('smtp_ports')->nullable();
            $table->unsignedInteger('messages_count')->default(0);
            $table->unsignedInteger('unread_count')->default(0);
            $table->timestamp('last_message_sent_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['connection_id', 'inbox_id'], 'mailtrap_inboxes_unique');
            $table->index(['tenant_id', 'status'], 'mailtrap_inboxes_status_idx');
            $table->index('updated_at', 'mailtrap_inboxes_updated_idx');
        });
    }

    public function down(): void
    {
        $table = config('mailtrap-core.tables.inboxes', 'mailtrap_inboxes');
        Schema::dropIfExists($table);
    }
};
