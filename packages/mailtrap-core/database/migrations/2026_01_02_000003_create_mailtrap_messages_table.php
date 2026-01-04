<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('mailtrap-core.tables.messages', 'mailtrap_messages');
        $connections = config('mailtrap-core.tables.connections', 'mailtrap_connections');
        $inboxes = config('mailtrap-core.tables.inboxes', 'mailtrap_inboxes');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) use ($connections, $inboxes) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('connection_id')->constrained($connections)->cascadeOnDelete();
            $table->foreignId('inbox_id')->constrained($inboxes)->cascadeOnDelete();
            $table->unsignedBigInteger('message_id');
            $table->string('subject')->nullable();
            $table->string('from_email')->nullable();
            $table->string('to_email')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->boolean('is_read')->default(false);
            $table->unsignedInteger('attachments_count')->default(0);
            $table->longText('html_body')->nullable();
            $table->longText('text_body')->nullable();
            $table->json('raw')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['inbox_id', 'message_id'], 'mailtrap_messages_unique');
            $table->index(['tenant_id', 'sent_at'], 'mailtrap_messages_sent_idx');
            $table->index('updated_at', 'mailtrap_messages_updated_idx');
        });
    }

    public function down(): void
    {
        $table = config('mailtrap-core.tables.messages', 'mailtrap_messages');
        Schema::dropIfExists($table);
    }
};
