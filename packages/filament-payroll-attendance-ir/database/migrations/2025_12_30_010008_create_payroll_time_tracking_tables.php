<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payroll_time_events')) {
            Schema::create('payroll_time_events', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')
                    ->constrained('tenants', 'id', 'payroll_time_event_tenant_fk')
                    ->cascadeOnDelete();
                $table->foreignId('company_id')
                    ->constrained('accounting_ir_companies', 'id', 'payroll_time_event_company_fk')
                    ->cascadeOnDelete();
                $table->foreignId('branch_id')
                    ->nullable()
                    ->constrained('accounting_ir_branches', 'id', 'payroll_time_event_branch_fk')
                    ->nullOnDelete();
                $table->foreignId('employee_id')
                    ->constrained('payroll_employees', 'id', 'payroll_time_event_emp_fk')
                    ->cascadeOnDelete();
                $table->dateTime('event_at');
                $table->string('event_type');
                $table->string('source')->default('manual');
                $table->string('device_ref')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->string('wifi_ssid')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('proof_type')->nullable();
                $table->json('proof_payload')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->foreignId('verified_by')
                    ->nullable()
                    ->constrained('users', 'id', 'payroll_time_event_verified_by_fk')
                    ->nullOnDelete();
                $table->dateTime('verified_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['employee_id', 'event_at'], 'payroll_time_event_emp_time_idx');
                $table->index(['tenant_id', 'company_id', 'branch_id'], 'payroll_time_event_tenant_company_branch_idx');
                $table->index(['event_type', 'source'], 'payroll_time_event_type_source_idx');
            });
        }

        if (! Schema::hasTable('payroll_time_breaks')) {
            Schema::create('payroll_time_breaks', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')
                    ->constrained('tenants', 'id', 'payroll_time_break_tenant_fk')
                    ->cascadeOnDelete();
                $table->foreignId('company_id')
                    ->constrained('accounting_ir_companies', 'id', 'payroll_time_break_company_fk')
                    ->cascadeOnDelete();
                $table->foreignId('branch_id')
                    ->nullable()
                    ->constrained('accounting_ir_branches', 'id', 'payroll_time_break_branch_fk')
                    ->nullOnDelete();
                $table->foreignId('employee_id')
                    ->constrained('payroll_employees', 'id', 'payroll_time_break_emp_fk')
                    ->cascadeOnDelete();
                $table->foreignId('time_event_id')
                    ->nullable()
                    ->constrained('payroll_time_events', 'id', 'payroll_time_break_event_fk')
                    ->nullOnDelete();
                $table->dateTime('started_at');
                $table->dateTime('ended_at')->nullable();
                $table->unsignedInteger('minutes')->default(0);
                $table->string('source')->default('manual');
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['employee_id', 'started_at'], 'payroll_time_break_emp_start_idx');
                $table->index(['tenant_id', 'company_id', 'branch_id'], 'payroll_time_break_tenant_company_branch_idx');
            });
        }

        if (! Schema::hasTable('payroll_timesheets')) {
            Schema::create('payroll_timesheets', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')
                    ->constrained('tenants', 'id', 'payroll_timesheet_tenant_fk')
                    ->cascadeOnDelete();
                $table->foreignId('company_id')
                    ->constrained('accounting_ir_companies', 'id', 'payroll_timesheet_company_fk')
                    ->cascadeOnDelete();
                $table->foreignId('branch_id')
                    ->nullable()
                    ->constrained('accounting_ir_branches', 'id', 'payroll_timesheet_branch_fk')
                    ->nullOnDelete();
                $table->foreignId('employee_id')
                    ->constrained('payroll_employees', 'id', 'payroll_timesheet_emp_fk')
                    ->cascadeOnDelete();
                $table->date('period_start');
                $table->date('period_end');
                $table->string('period_type')->default('daily');
                $table->string('status')->default('draft');
                $table->unsignedInteger('worked_minutes')->default(0);
                $table->unsignedInteger('overtime_minutes')->default(0);
                $table->unsignedInteger('night_minutes')->default(0);
                $table->unsignedInteger('friday_minutes')->default(0);
                $table->unsignedInteger('holiday_minutes')->default(0);
                $table->unsignedInteger('late_minutes')->default(0);
                $table->unsignedInteger('early_leave_minutes')->default(0);
                $table->unsignedInteger('absence_minutes')->default(0);
                $table->foreignId('approved_by')
                    ->nullable()
                    ->constrained('users', 'id', 'payroll_timesheet_approved_fk')
                    ->nullOnDelete();
                $table->dateTime('approved_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->unique(['employee_id', 'period_start', 'period_end', 'period_type'], 'payroll_timesheet_emp_period_uniq');
                $table->index(['tenant_id', 'company_id', 'branch_id', 'status'], 'payroll_timesheet_tenant_company_branch_status_idx');
                $table->index(['employee_id', 'period_start'], 'payroll_timesheet_emp_start_idx');
            });
        }

        if (! Schema::hasTable('payroll_overtime_requests')) {
            Schema::create('payroll_overtime_requests', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')
                    ->constrained('tenants', 'id', 'payroll_ot_tenant_fk')
                    ->cascadeOnDelete();
                $table->foreignId('company_id')
                    ->constrained('accounting_ir_companies', 'id', 'payroll_ot_company_fk')
                    ->cascadeOnDelete();
                $table->foreignId('branch_id')
                    ->nullable()
                    ->constrained('accounting_ir_branches', 'id', 'payroll_ot_branch_fk')
                    ->nullOnDelete();
                $table->foreignId('employee_id')
                    ->constrained('payroll_employees', 'id', 'payroll_ot_emp_fk')
                    ->cascadeOnDelete();
                $table->date('work_date');
                $table->unsignedInteger('requested_minutes')->default(0);
                $table->string('status')->default('pending');
                $table->foreignId('requested_by')
                    ->nullable()
                    ->constrained('users', 'id', 'payroll_ot_requested_by_fk')
                    ->nullOnDelete();
                $table->foreignId('approved_by')
                    ->nullable()
                    ->constrained('users', 'id', 'payroll_ot_approved_by_fk')
                    ->nullOnDelete();
                $table->dateTime('approved_at')->nullable();
                $table->text('reason')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'company_id', 'branch_id', 'status'], 'payroll_ot_tenant_company_branch_status_idx');
                $table->index(['employee_id', 'work_date'], 'payroll_ot_emp_date_idx');
            });
        }

        if (! Schema::hasTable('payroll_attendance_exceptions')) {
            Schema::create('payroll_attendance_exceptions', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tenant_id')
                    ->constrained('tenants', 'id', 'payroll_exc_tenant_fk')
                    ->cascadeOnDelete();
                $table->foreignId('company_id')
                    ->constrained('accounting_ir_companies', 'id', 'payroll_exc_company_fk')
                    ->cascadeOnDelete();
                $table->foreignId('branch_id')
                    ->nullable()
                    ->constrained('accounting_ir_branches', 'id', 'payroll_exc_branch_fk')
                    ->nullOnDelete();
                $table->foreignId('employee_id')
                    ->nullable()
                    ->constrained('payroll_employees', 'id', 'payroll_exc_emp_fk')
                    ->nullOnDelete();
                $table->foreignId('attendance_record_id')
                    ->nullable()
                    ->constrained('payroll_attendance_records', 'id', 'payroll_exc_att_fk')
                    ->nullOnDelete();
                $table->foreignId('time_event_id')
                    ->nullable()
                    ->constrained('payroll_time_events', 'id', 'payroll_exc_event_fk')
                    ->nullOnDelete();
                $table->foreignId('timesheet_id')
                    ->nullable()
                    ->constrained('payroll_timesheets', 'id', 'payroll_exc_timesheet_fk')
                    ->nullOnDelete();
                $table->string('type');
                $table->string('severity')->default('low');
                $table->string('status')->default('open');
                $table->dateTime('detected_at');
                $table->foreignId('assigned_to')
                    ->nullable()
                    ->constrained('users', 'id', 'payroll_exc_assigned_fk')
                    ->nullOnDelete();
                $table->foreignId('resolved_by')
                    ->nullable()
                    ->constrained('users', 'id', 'payroll_exc_resolved_fk')
                    ->nullOnDelete();
                $table->dateTime('resolved_at')->nullable();
                $table->text('resolution_notes')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'company_id', 'branch_id', 'status'], 'payroll_exc_tenant_company_branch_status_idx');
                $table->index(['employee_id', 'detected_at'], 'payroll_exc_emp_detected_idx');
                $table->index(['assigned_to', 'status'], 'payroll_exc_assigned_status_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_attendance_exceptions');
        Schema::dropIfExists('payroll_overtime_requests');
        Schema::dropIfExists('payroll_timesheets');
        Schema::dropIfExists('payroll_time_breaks');
        Schema::dropIfExists('payroll_time_events');
    }
};
