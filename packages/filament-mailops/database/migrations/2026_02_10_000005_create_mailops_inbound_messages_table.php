<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('filament-mailops.tables.inbound_messages', 'mailops_inbound_messages');
        $domainsTable = config('filament-mailops.tables.domains', 'mailops_domains');
        $mailboxesTable = config('filament-mailops.tables.mailboxes', 'mailops_mailboxes');

        if (Schema::hasTable($tableName)) {
            return;
        }

        Schema::create($tableName, function (Blueprint $table) use ($domainsTable, $mailboxesTable) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('domain_id')->constrained($domainsTable)->cascadeOnDelete();
            $table->foreignId('mailbox_id')->constrained($mailboxesTable)->cascadeOnDelete();
            $table->string('message_uid');
            $table->string('message_id')->nullable();
            $table->string('subject')->nullable();
            $table->string('from_email')->nullable();
            $table->json('to_emails')->nullable();
            $table->json('cc_emails')->nullable();
            $table->json('bcc_emails')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->boolean('is_seen')->default(false);
            $table->longText('html_body')->nullable();
            $table->longText('text_body')->nullable();
            $table->json('raw_headers')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['mailbox_id', 'message_uid']);
            $table->index(['tenant_id', 'mailbox_id']);
            $table->index(['tenant_id', 'received_at']);
            $table->index(['tenant_id', 'updated_at']);
        });
    }

    public function down(): void
    {
        $tableName = config('filament-mailops.tables.inbound_messages', 'mailops_inbound_messages');
        Schema::dropIfExists($tableName);
    }
};
