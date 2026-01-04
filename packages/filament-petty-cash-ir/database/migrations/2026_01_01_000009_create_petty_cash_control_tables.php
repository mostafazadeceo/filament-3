<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_control_exceptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'petty_cash_exception_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'petty_cash_exception_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('fund_id')
                ->nullable()
                ->constrained('petty_cash_funds', 'id', 'petty_cash_exception_fund_fk')
                ->nullOnDelete();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('rule_code', 64);
            $table->string('severity', 16)->default('medium');
            $table->string('status', 16)->default('open');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('detected_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('detected_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_exception_detected_fk')
                ->nullOnDelete();
            $table->foreignId('resolved_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_exception_resolved_fk')
                ->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'status'], 'petty_cash_exception_scope_idx');
            $table->index(['rule_code'], 'petty_cash_exception_rule_idx');
            $table->index(['subject_type', 'subject_id'], 'petty_cash_exception_subject_idx');
        });

        Schema::create('petty_cash_cash_counts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'petty_cash_count_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'petty_cash_count_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('fund_id')
                ->constrained('petty_cash_funds', 'id', 'petty_cash_count_fund_fk')
                ->cascadeOnDelete();
            $table->date('count_date');
            $table->string('status', 16)->default('draft');
            $table->decimal('expected_balance', 18, 2)->default(0);
            $table->decimal('counted_balance', 18, 2)->default(0);
            $table->decimal('variance', 18, 2)->default(0);
            $table->foreignId('counted_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_count_counted_fk')
                ->nullOnDelete();
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_count_approved_fk')
                ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'fund_id', 'status'], 'petty_cash_count_scope_idx');
        });

        Schema::create('petty_cash_reconciliations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'petty_cash_reconcile_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'petty_cash_reconcile_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('fund_id')
                ->constrained('petty_cash_funds', 'id', 'petty_cash_reconcile_fund_fk')
                ->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('status', 16)->default('draft');
            $table->decimal('expected_balance', 18, 2)->default(0);
            $table->decimal('ledger_balance', 18, 2)->default(0);
            $table->decimal('variance', 18, 2)->default(0);
            $table->foreignId('prepared_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_reconcile_prepared_fk')
                ->nullOnDelete();
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_reconcile_approved_fk')
                ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'fund_id', 'status'], 'petty_cash_reconcile_scope_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_reconciliations');
        Schema::dropIfExists('petty_cash_cash_counts');
        Schema::dropIfExists('petty_cash_control_exceptions');
    }
};
