<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ir_journal_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->foreignId('fiscal_year_id')->constrained('accounting_ir_fiscal_years')->cascadeOnDelete();
            $table->foreignId('fiscal_period_id')->nullable()->constrained('accounting_ir_fiscal_periods')->nullOnDelete();
            $table->string('entry_no');
            $table->date('entry_date');
            $table->string('status')->default('draft');
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('total_debit', 18, 2)->default(0);
            $table->decimal('total_credit', 18, 2)->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('reversed_entry_id')->nullable()->constrained('accounting_ir_journal_entries')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'fiscal_year_id', 'entry_no'], 'air_journal_entries_company_year_no_uniq');
            $table->index(['tenant_id', 'company_id', 'entry_date'], 'air_journal_entries_tenant_company_date_idx');
            $table->index(['tenant_id', 'status'], 'air_journal_entries_tenant_status_idx');
        });

        Schema::create('accounting_ir_journal_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('journal_entry_id')->constrained('accounting_ir_journal_entries')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounting_ir_chart_accounts')->restrictOnDelete();
            $table->text('description')->nullable();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);
            $table->string('currency')->default('IRR');
            $table->decimal('amount', 18, 2)->nullable();
            $table->decimal('exchange_rate', 18, 6)->nullable();
            $table->json('dimensions')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'journal_entry_id'], 'air_journal_lines_tenant_company_entry_idx');
            $table->index(['tenant_id', 'account_id'], 'air_journal_lines_tenant_account_idx');
        });

        Schema::create('accounting_ir_journal_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('journal_entry_id')->constrained('accounting_ir_journal_entries')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'journal_entry_id'], 'air_journal_attach_tenant_company_entry_idx');
        });

        Schema::create('accounting_ir_journal_approvals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('journal_entry_id')->constrained('accounting_ir_journal_entries')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'journal_entry_id'], 'air_journal_approvals_tenant_company_entry_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ir_journal_approvals');
        Schema::dropIfExists('accounting_ir_journal_attachments');
        Schema::dropIfExists('accounting_ir_journal_lines');
        Schema::dropIfExists('accounting_ir_journal_entries');
    }
};
