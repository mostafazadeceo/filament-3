<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_attendance_policies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_policy_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_policy_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_policy_branch_fk')
                ->nullOnDelete();
            $table->string('name');
            $table->string('status')->default('active');
            $table->boolean('is_default')->default(false);
            $table->boolean('requires_consent')->default(true);
            $table->boolean('allow_remote_work')->default(false);
            $table->json('rules')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id', 'branch_id', 'status'], 'payroll_policy_tenant_company_branch_status_idx');
            $table->index(['company_id', 'is_default'], 'payroll_policy_company_default_idx');
        });

        Schema::create('payroll_work_calendars', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_calendar_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_calendar_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_calendar_branch_fk')
                ->nullOnDelete();
            $table->string('name');
            $table->string('calendar_type')->default('jalali');
            $table->string('timezone')->default('Asia/Tehran');
            $table->boolean('is_default')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'name'], 'payroll_calendar_company_name_uniq');
            $table->index(['tenant_id', 'company_id', 'branch_id', 'is_default'], 'payroll_calendar_tenant_company_branch_default_idx');
        });

        Schema::create('payroll_holiday_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_holiday_rule_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_holiday_rule_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('work_calendar_id')
                ->constrained('payroll_work_calendars', 'id', 'payroll_holiday_rule_calendar_fk')
                ->cascadeOnDelete();
            $table->date('holiday_date');
            $table->string('title');
            $table->boolean('is_public')->default(true);
            $table->string('source')->default('manual');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['work_calendar_id', 'holiday_date'], 'payroll_holiday_rule_calendar_date_uniq');
            $table->index(['tenant_id', 'company_id', 'holiday_date'], 'payroll_holiday_rule_tenant_company_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_holiday_rules');
        Schema::dropIfExists('payroll_work_calendars');
        Schema::dropIfExists('payroll_attendance_policies');
    }
};
