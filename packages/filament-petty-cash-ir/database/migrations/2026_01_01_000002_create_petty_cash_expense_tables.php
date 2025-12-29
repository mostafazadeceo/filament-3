<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_expenses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'petty_cash_exp_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'petty_cash_exp_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'petty_cash_exp_branch_fk')
                ->nullOnDelete();
            $table->foreignId('fund_id')
                ->constrained('petty_cash_funds', 'id', 'petty_cash_exp_fund_fk')
                ->cascadeOnDelete();
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('petty_cash_categories', 'id', 'petty_cash_exp_category_fk')
                ->nullOnDelete();
            $table->foreignId('requested_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_exp_requested_fk')
                ->nullOnDelete();
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_exp_approved_fk')
                ->nullOnDelete();
            $table->foreignId('paid_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_exp_paid_fk')
                ->nullOnDelete();
            $table->foreignId('accounting_party_id')
                ->nullable()
                ->constrained('accounting_ir_parties', 'id', 'petty_cash_exp_party_fk')
                ->nullOnDelete();
            $table->foreignId('accounting_journal_entry_id')
                ->nullable()
                ->constrained('accounting_ir_journal_entries', 'id', 'petty_cash_exp_journal_fk')
                ->nullOnDelete();
            $table->date('expense_date');
            $table->decimal('amount', 18, 2);
            $table->string('currency')->default('IRR');
            $table->string('status')->default('draft');
            $table->string('reference')->nullable();
            $table->string('payee_name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('receipt_required')->default(true);
            $table->boolean('has_receipt')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id', 'branch_id', 'fund_id', 'status'], 'petty_cash_exp_tenant_company_branch_fund_status_idx');
            $table->index(['tenant_id', 'company_id', 'expense_date'], 'petty_cash_exp_tenant_company_date_idx');
        });

        Schema::create('petty_cash_expense_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'petty_cash_exp_attach_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'petty_cash_exp_attach_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('expense_id')
                ->constrained('petty_cash_expenses', 'id', 'petty_cash_exp_attach_expense_fk')
                ->cascadeOnDelete();
            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_exp_attach_uploaded_fk')
                ->nullOnDelete();
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'expense_id'], 'petty_cash_exp_attach_tenant_company_expense_idx');
        });

        Schema::create('petty_cash_replenishments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'petty_cash_rep_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'petty_cash_rep_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'petty_cash_rep_branch_fk')
                ->nullOnDelete();
            $table->foreignId('fund_id')
                ->constrained('petty_cash_funds', 'id', 'petty_cash_rep_fund_fk')
                ->cascadeOnDelete();
            $table->foreignId('requested_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_rep_requested_fk')
                ->nullOnDelete();
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_rep_approved_fk')
                ->nullOnDelete();
            $table->foreignId('paid_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_rep_paid_fk')
                ->nullOnDelete();
            $table->foreignId('source_treasury_account_id')
                ->nullable()
                ->constrained('accounting_ir_treasury_accounts', 'id', 'petty_cash_rep_treasury_fk')
                ->nullOnDelete();
            $table->foreignId('accounting_journal_entry_id')
                ->nullable()
                ->constrained('accounting_ir_journal_entries', 'id', 'petty_cash_rep_journal_fk')
                ->nullOnDelete();
            $table->foreignId('accounting_treasury_transaction_id')
                ->nullable()
                ->constrained('accounting_ir_treasury_transactions', 'id', 'petty_cash_rep_treasury_trx_fk')
                ->nullOnDelete();
            $table->date('request_date');
            $table->decimal('amount', 18, 2);
            $table->string('currency')->default('IRR');
            $table->string('status')->default('draft');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'fund_id', 'status'], 'petty_cash_rep_tenant_company_fund_status_idx');
            $table->index(['tenant_id', 'company_id', 'request_date'], 'petty_cash_rep_tenant_company_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_replenishments');
        Schema::dropIfExists('petty_cash_expense_attachments');
        Schema::dropIfExists('petty_cash_expenses');
    }
};
