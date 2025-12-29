<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_audit_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'petty_cash_audit_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'petty_cash_audit_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('fund_id')
                ->nullable()
                ->constrained('petty_cash_funds', 'id', 'petty_cash_audit_fund_fk')
                ->nullOnDelete();
            $table->foreignId('actor_id')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_audit_actor_fk')
                ->nullOnDelete();
            $table->string('event_type');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'company_id', 'event_type'], 'petty_cash_audit_tenant_company_event_idx');
            $table->index(['subject_type', 'subject_id'], 'petty_cash_audit_subject_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_audit_events');
    }
};
