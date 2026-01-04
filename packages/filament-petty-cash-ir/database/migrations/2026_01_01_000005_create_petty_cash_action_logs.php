<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_action_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'petty_cash_action_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'petty_cash_action_company_fk')
                ->cascadeOnDelete();
            $table->string('action', 64);
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->string('idempotency_key', 64);
            $table->string('status')->default('completed');
            $table->foreignId('actor_id')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_action_actor_fk')
                ->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique([
                'tenant_id',
                'action',
                'subject_type',
                'subject_id',
                'idempotency_key',
            ], 'petty_cash_action_log_uniq');
            $table->index(['tenant_id', 'company_id', 'action'], 'petty_cash_action_log_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_action_logs');
    }
};
