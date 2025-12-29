<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_ir_loans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->foreignId('employee_id')->constrained('payroll_ir_employees')->cascadeOnDelete();
            $table->decimal('amount', 18, 2)->default(0);
            $table->decimal('interest_rate', 6, 4)->default(0);
            $table->unsignedInteger('installment_count')->default(1);
            $table->decimal('installment_amount', 18, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'status'], 'pir_loan_employee_status_idx');
        });

        Schema::create('payroll_ir_loan_installments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('loan_id')->constrained('payroll_ir_loans')->cascadeOnDelete();
            $table->date('due_date');
            $table->decimal('amount', 18, 2)->default(0);
            $table->dateTime('paid_at')->nullable();
            $table->string('status')->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['loan_id', 'due_date'], 'pir_loan_installment_due_idx');
        });

        Schema::create('payroll_ir_advances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->foreignId('employee_id')->constrained('payroll_ir_employees')->cascadeOnDelete();
            $table->decimal('amount', 18, 2)->default(0);
            $table->dateTime('requested_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('payroll_run_id')->nullable()->constrained('payroll_ir_payroll_runs')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'status'], 'pir_advance_employee_status_idx');
            $table->index(['payroll_run_id'], 'pir_advance_run_idx');
        });

        Schema::create('payroll_ir_settlements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->foreignId('employee_id')->constrained('payroll_ir_employees')->cascadeOnDelete();
            $table->date('settlement_date')->nullable();
            $table->string('status')->default('draft');
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'status'], 'pir_settlement_employee_status_idx');
        });

        Schema::create('payroll_ir_settlement_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('settlement_id')->constrained('payroll_ir_settlements')->cascadeOnDelete();
            $table->string('item_type')->default('allowance');
            $table->string('title');
            $table->decimal('amount', 18, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['settlement_id'], 'pir_settlement_item_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_ir_settlement_items');
        Schema::dropIfExists('payroll_ir_settlements');
        Schema::dropIfExists('payroll_ir_advances');
        Schema::dropIfExists('payroll_ir_loan_installments');
        Schema::dropIfExists('payroll_ir_loans');
    }
};
