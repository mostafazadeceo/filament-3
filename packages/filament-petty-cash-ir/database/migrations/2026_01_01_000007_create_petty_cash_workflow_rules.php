<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_workflow_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'petty_cash_workflow_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'petty_cash_workflow_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('fund_id')
                ->nullable()
                ->constrained('petty_cash_funds', 'id', 'petty_cash_workflow_fund_fk')
                ->nullOnDelete();
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('petty_cash_categories', 'id', 'petty_cash_workflow_category_fk')
                ->nullOnDelete();
            $table->string('transaction_type', 32); // expense | replenishment
            $table->decimal('min_amount', 18, 2)->nullable();
            $table->decimal('max_amount', 18, 2)->nullable();
            $table->unsignedSmallInteger('steps_required')->default(1);
            $table->boolean('require_separation')->default(false);
            $table->boolean('require_receipt')->nullable();
            $table->string('status', 16)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'transaction_type', 'status'], 'petty_cash_workflow_scope_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_workflow_rules');
    }
};
