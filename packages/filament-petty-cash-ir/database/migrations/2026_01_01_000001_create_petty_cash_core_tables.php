<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_funds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'petty_cash_fund_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'petty_cash_fund_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'petty_cash_fund_branch_fk')
                ->nullOnDelete();
            $table->foreignId('custodian_user_id')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_fund_custodian_fk')
                ->nullOnDelete();
            $table->foreignId('accounting_cash_account_id')
                ->nullable()
                ->constrained('accounting_ir_chart_accounts', 'id', 'petty_cash_fund_cash_account_fk')
                ->nullOnDelete();
            $table->foreignId('accounting_source_account_id')
                ->nullable()
                ->constrained('accounting_ir_chart_accounts', 'id', 'petty_cash_fund_source_account_fk')
                ->nullOnDelete();
            $table->foreignId('default_expense_account_id')
                ->nullable()
                ->constrained('accounting_ir_chart_accounts', 'id', 'petty_cash_fund_expense_account_fk')
                ->nullOnDelete();
            $table->foreignId('accounting_treasury_account_id')
                ->nullable()
                ->constrained('accounting_ir_treasury_accounts', 'id', 'petty_cash_fund_treasury_account_fk')
                ->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('status')->default('active');
            $table->string('currency')->default('IRR');
            $table->decimal('opening_balance', 18, 2)->default(0);
            $table->decimal('current_balance', 18, 2)->default(0);
            $table->decimal('threshold_balance', 18, 2)->default(0);
            $table->decimal('replenishment_amount', 18, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code'], 'petty_cash_fund_company_code_uniq');
            $table->index(['tenant_id', 'company_id', 'branch_id', 'status'], 'petty_cash_fund_tenant_company_branch_status_idx');
        });

        Schema::create('petty_cash_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'petty_cash_cat_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'petty_cash_cat_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('accounting_account_id')
                ->nullable()
                ->constrained('accounting_ir_chart_accounts', 'id', 'petty_cash_cat_account_fk')
                ->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('status')->default('active');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code'], 'petty_cash_cat_company_code_uniq');
            $table->index(['tenant_id', 'company_id', 'status'], 'petty_cash_cat_tenant_company_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_categories');
        Schema::dropIfExists('petty_cash_funds');
    }
};
