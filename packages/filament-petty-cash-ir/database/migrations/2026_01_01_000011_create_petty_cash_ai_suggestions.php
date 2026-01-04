<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_ai_suggestions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'petty_cash_ai_suggestion_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->nullable()
                ->constrained('accounting_ir_companies', 'id', 'petty_cash_ai_suggestion_company_fk')
                ->nullOnDelete();
            $table->foreignId('fund_id')
                ->nullable()
                ->constrained('petty_cash_funds', 'id', 'petty_cash_ai_suggestion_fund_fk')
                ->nullOnDelete();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('suggestion_type');
            $table->string('status')->default('proposed');
            $table->decimal('score', 5, 2)->nullable();
            $table->string('provider');
            $table->string('input_hash')->nullable();
            $table->json('suggested_payload')->nullable();
            $table->json('reasons')->nullable();
            $table->json('input_payload')->nullable();
            $table->json('output_payload')->nullable();
            $table->foreignId('requested_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_ai_suggestion_requested_fk')
                ->nullOnDelete();
            $table->foreignId('decided_by')
                ->nullable()
                ->constrained('users', 'id', 'petty_cash_ai_suggestion_decided_fk')
                ->nullOnDelete();
            $table->dateTime('decided_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'suggestion_type'], 'petty_cash_ai_suggestion_tenant_company_idx');
            $table->index(['tenant_id', 'status'], 'petty_cash_ai_suggestion_status_idx');
            $table->index(['subject_type', 'subject_id'], 'petty_cash_ai_suggestion_subject_idx');
            $table->index(['input_hash'], 'petty_cash_ai_suggestion_input_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_ai_suggestions');
    }
};
