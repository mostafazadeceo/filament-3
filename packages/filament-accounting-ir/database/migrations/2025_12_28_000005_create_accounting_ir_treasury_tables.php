<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_ir_treasury_accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->string('account_type')->default('bank');
            $table->string('name');
            $table->string('account_no')->nullable();
            $table->string('iban')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('currency')->default('IRR');
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'account_type'], 'air_treasury_accounts_tenant_company_type_idx');
        });

        Schema::create('accounting_ir_treasury_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('treasury_account_id')->constrained('accounting_ir_treasury_accounts')->cascadeOnDelete();
            $table->string('transaction_type')->default('deposit');
            $table->date('transaction_date');
            $table->decimal('amount', 18, 2)->default(0);
            $table->string('currency')->default('IRR');
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'transaction_date'], 'air_treasury_transactions_tenant_company_date_idx');
        });

        Schema::create('accounting_ir_cheques', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('party_id')->nullable()->constrained('accounting_ir_parties')->nullOnDelete();
            $table->foreignId('treasury_account_id')->nullable()->constrained('accounting_ir_treasury_accounts')->nullOnDelete();
            $table->string('direction')->default('received');
            $table->string('cheque_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('amount', 18, 2)->default(0);
            $table->string('status')->default('issued');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'status'], 'air_cheques_tenant_company_status_idx');
        });

        Schema::create('accounting_ir_cheque_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cheque_id')->constrained('accounting_ir_cheques')->cascadeOnDelete();
            $table->date('event_date');
            $table->string('status');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['cheque_id', 'status'], 'air_cheque_events_cheque_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ir_cheque_events');
        Schema::dropIfExists('accounting_ir_cheques');
        Schema::dropIfExists('accounting_ir_treasury_transactions');
        Schema::dropIfExists('accounting_ir_treasury_accounts');
    }
};
