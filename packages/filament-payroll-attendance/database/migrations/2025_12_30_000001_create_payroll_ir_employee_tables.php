<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_ir_employees', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->string('employee_no')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('national_id')->nullable();
            $table->date('birth_date')->nullable();
            $table->date('hire_date')->nullable();
            $table->string('employment_type')->default('full_time');
            $table->string('status')->default('active');
            $table->string('gender')->nullable();
            $table->string('marital_status')->default('single');
            $table->unsignedSmallInteger('children_count')->default(0);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('bank_iban')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_card')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'company_id', 'employee_no'], 'pir_employee_no_unique');
            $table->unique(['tenant_id', 'company_id', 'national_id'], 'pir_employee_national_unique');
            $table->index(['tenant_id', 'company_id', 'status'], 'pir_employee_tenant_company_status_idx');
            $table->index(['company_id', 'branch_id'], 'pir_employee_company_branch_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_ir_employees');
    }
};
