<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('filament-mailops.tables.mailboxes', 'mailops_mailboxes');
        $domainsTable = config('filament-mailops.tables.domains', 'mailops_domains');

        if (Schema::hasTable($tableName)) {
            return;
        }

        Schema::create($tableName, function (Blueprint $table) use ($domainsTable) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('domain_id')->constrained($domainsTable)->cascadeOnDelete();
            $table->string('local_part');
            $table->string('email');
            $table->string('display_name')->nullable();
            $table->text('password');
            $table->string('status')->default('active');
            $table->unsignedBigInteger('quota_bytes')->nullable();
            $table->json('settings')->nullable();
            $table->string('sync_status')->default('pending');
            $table->text('last_error')->nullable();
            $table->timestamp('mailu_synced_at')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'email']);
            $table->index(['tenant_id', 'domain_id']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'updated_at']);
        });
    }

    public function down(): void
    {
        $tableName = config('filament-mailops.tables.mailboxes', 'mailops_mailboxes');
        Schema::dropIfExists($tableName);
    }
};
