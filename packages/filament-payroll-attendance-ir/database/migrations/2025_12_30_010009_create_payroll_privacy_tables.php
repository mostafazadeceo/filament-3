<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_employee_consents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_consent_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_consent_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_consent_branch_fk')
                ->nullOnDelete();
            $table->foreignId('employee_id')
                ->constrained('payroll_employees', 'id', 'payroll_consent_emp_fk')
                ->cascadeOnDelete();
            $table->string('consent_type');
            $table->boolean('is_granted')->default(false);
            $table->foreignId('granted_by')
                ->nullable()
                ->constrained('users', 'id', 'payroll_consent_granted_by_fk')
                ->nullOnDelete();
            $table->dateTime('granted_at')->nullable();
            $table->dateTime('revoked_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'consent_type'], 'payroll_consent_emp_type_uniq');
            $table->index(['tenant_id', 'company_id', 'consent_type'], 'payroll_consent_tenant_company_type_idx');
        });

        Schema::create('payroll_sensitive_access_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_sensitive_log_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_sensitive_log_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_sensitive_log_branch_fk')
                ->nullOnDelete();
            $table->foreignId('actor_id')
                ->nullable()
                ->constrained('users', 'id', 'payroll_sensitive_log_actor_fk')
                ->nullOnDelete();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('reason');
            $table->json('metadata')->nullable();
            $table->dateTime('created_at');

            $table->index(['tenant_id', 'company_id', 'branch_id'], 'payroll_sensitive_log_tenant_company_branch_idx');
            $table->index(['subject_type', 'subject_id'], 'payroll_sensitive_log_subject_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_sensitive_access_logs');
        Schema::dropIfExists('payroll_employee_consents');
    }
};
