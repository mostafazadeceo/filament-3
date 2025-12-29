<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_settlements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'petty_cash_settle_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'petty_cash_settle_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'petty_cash_settle_branch_fk')
                ->nullOnDelete();
            $table->foreignId('fund_id')
                ->constrained('petty_cash_funds', 'id', 'petty_cash_settle_fund_fk')
                ->cascadeOnDelete();
            $table->foreignId('requested_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_settle_requested_fk')
                ->nullOnDelete();
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_settle_approved_fk')
                ->nullOnDelete();
            $table->foreignId('posted_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_settle_posted_fk')
                ->nullOnDelete();
            $table->foreignId('accounting_journal_entry_id')
                ->nullable()
                ->constrained('accounting_ir_journal_entries', 'id', 'petty_cash_settle_journal_fk')
                ->nullOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('status')->default('draft');
            $table->decimal('total_expenses', 18, 2)->default(0);
            $table->decimal('total_replenished', 18, 2)->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['fund_id', 'period_start', 'period_end'], 'petty_cash_settle_fund_period_uniq');
            $table->index(['tenant_id', 'company_id', 'fund_id', 'status'], 'petty_cash_settle_tenant_company_fund_status_idx');
        });

        Schema::create('petty_cash_settlement_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'petty_cash_settle_item_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'petty_cash_settle_item_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('settlement_id')
                ->constrained('petty_cash_settlements', 'id', 'petty_cash_settle_item_settle_fk')
                ->cascadeOnDelete();
            $table->foreignId('expense_id')
                ->constrained('petty_cash_expenses', 'id', 'petty_cash_settle_item_expense_fk')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['settlement_id', 'expense_id'], 'petty_cash_settle_item_settle_expense_uniq');
            $table->unique(['expense_id'], 'petty_cash_settle_item_expense_uniq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_settlement_items');
        Schema::dropIfExists('petty_cash_settlements');
    }
};
