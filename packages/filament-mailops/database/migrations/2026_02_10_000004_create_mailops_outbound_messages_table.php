<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('filament-mailops.tables.outbound_messages', 'mailops_outbound_messages');
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
            $table->string('from_email');
            $table->json('to_emails');
            $table->json('cc_emails')->nullable();
            $table->json('bcc_emails')->nullable();
            $table->string('subject')->nullable();
            $table->longText('html_body')->nullable();
            $table->longText('text_body')->nullable();
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->string('provider_message_id')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'mailbox_id']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'sent_at']);
            $table->index(['tenant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        $tableName = config('filament-mailops.tables.outbound_messages', 'mailops_outbound_messages');
        Schema::dropIfExists($tableName);
    }
};
