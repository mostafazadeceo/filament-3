<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_attendance_shifts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_shift_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_shift_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_shift_branch_fk')
                ->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('break_minutes')->default(0);
            $table->boolean('is_night')->default(false);
            $table->boolean('is_rotating')->default(false);
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'name'], 'payroll_shift_company_name_uniq');
            $table->index(['tenant_id', 'company_id', 'branch_id', 'is_active'], 'payroll_shift_tenant_company_branch_active_idx');
        });

        Schema::create('payroll_attendance_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_schedule_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_schedule_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_schedule_branch_fk')
                ->nullOnDelete();
            $table->foreignId('employee_id')
                ->constrained('payroll_employees', 'id', 'payroll_schedule_emp_fk')
                ->cascadeOnDelete();
            $table->foreignId('shift_id')
                ->nullable()
                ->constrained('payroll_attendance_shifts', 'id', 'payroll_schedule_shift_fk')
                ->nullOnDelete();
            $table->date('work_date');
            $table->string('status')->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'work_date'], 'payroll_schedule_emp_date_uniq');
            $table->index(['tenant_id', 'company_id', 'branch_id', 'work_date'], 'payroll_schedule_tenant_company_branch_date_idx');
        });

        Schema::create('payroll_time_punches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_punch_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_punch_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_punch_branch_fk')
                ->nullOnDelete();
            $table->foreignId('employee_id')
                ->constrained('payroll_employees', 'id', 'payroll_punch_emp_fk')
                ->cascadeOnDelete();
            $table->dateTime('punch_at');
            $table->string('type');
            $table->string('source')->default('manual');
            $table->string('device_ref')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'punch_at'], 'payroll_punch_emp_time_idx');
            $table->index(['tenant_id', 'company_id', 'branch_id'], 'payroll_punch_tenant_company_branch_idx');
        });

        Schema::create('payroll_attendance_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_att_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_att_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_att_branch_fk')
                ->nullOnDelete();
            $table->foreignId('employee_id')
                ->constrained('payroll_employees', 'id', 'payroll_att_emp_fk')
                ->cascadeOnDelete();
            $table->foreignId('shift_id')
                ->nullable()
                ->constrained('payroll_attendance_shifts', 'id', 'payroll_att_shift_fk')
                ->nullOnDelete();
            $table->date('work_date');
            $table->dateTime('scheduled_in')->nullable();
            $table->dateTime('scheduled_out')->nullable();
            $table->dateTime('actual_in')->nullable();
            $table->dateTime('actual_out')->nullable();
            $table->unsignedInteger('worked_minutes')->default(0);
            $table->unsignedInteger('late_minutes')->default(0);
            $table->unsignedInteger('early_leave_minutes')->default(0);
            $table->unsignedInteger('overtime_minutes')->default(0);
            $table->unsignedInteger('night_minutes')->default(0);
            $table->unsignedInteger('friday_minutes')->default(0);
            $table->unsignedInteger('holiday_minutes')->default(0);
            $table->unsignedInteger('absence_minutes')->default(0);
            $table->string('status')->default('draft');
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users', 'id', 'payroll_att_approved_by_fk')
                ->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'work_date'], 'payroll_att_emp_date_uniq');
            $table->index(['tenant_id', 'company_id', 'branch_id', 'work_date'], 'payroll_att_tenant_company_branch_date_idx');
            $table->index(['status', 'approved_at'], 'payroll_att_status_approved_idx');
        });

        Schema::create('payroll_leave_types', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_leave_type_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_leave_type_company_fk')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('type')->default('paid');
            $table->decimal('default_days_per_year', 6, 2)->default(0);
            $table->boolean('requires_approval')->default(true);
            $table->boolean('requires_document')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'name'], 'payroll_leave_type_company_name_uniq');
            $table->index(['tenant_id', 'company_id', 'is_active'], 'payroll_leave_type_tenant_company_active_idx');
        });

        Schema::create('payroll_leave_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_leave_req_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_leave_req_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_leave_req_branch_fk')
                ->nullOnDelete();
            $table->foreignId('employee_id')
                ->constrained('payroll_employees', 'id', 'payroll_leave_req_emp_fk')
                ->cascadeOnDelete();
            $table->foreignId('leave_type_id')
                ->constrained('payroll_leave_types', 'id', 'payroll_leave_req_type_fk')
                ->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('duration_hours', 6, 2)->default(0);
            $table->string('status')->default('pending');
            $table->foreignId('requested_by')
                ->nullable()
                ->constrained('users', 'id', 'payroll_leave_req_requested_fk')
                ->nullOnDelete();
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users', 'id', 'payroll_leave_req_approved_fk')
                ->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'branch_id', 'status'], 'payroll_leave_req_tenant_company_branch_status_idx');
            $table->index(['employee_id', 'start_date', 'end_date'], 'payroll_leave_req_emp_date_idx');
        });

        Schema::create('payroll_missions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_mission_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_mission_company_fk')
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('accounting_ir_branches', 'id', 'payroll_mission_branch_fk')
                ->nullOnDelete();
            $table->foreignId('employee_id')
                ->constrained('payroll_employees', 'id', 'payroll_mission_emp_fk')
                ->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('allowance_amount', 18, 2)->default(0);
            $table->string('status')->default('pending');
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users', 'id', 'payroll_mission_approved_fk')
                ->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'branch_id', 'status'], 'payroll_mission_tenant_company_branch_status_idx');
        });

        Schema::create('payroll_holidays', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants', 'id', 'payroll_holiday_tenant_fk')
                ->cascadeOnDelete();
            $table->foreignId('company_id')
                ->constrained('accounting_ir_companies', 'id', 'payroll_holiday_company_fk')
                ->cascadeOnDelete();
            $table->date('holiday_date');
            $table->string('title');
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'holiday_date'], 'payroll_holiday_company_date_uniq');
            $table->index(['tenant_id', 'company_id', 'is_public'], 'payroll_holiday_tenant_company_public_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_holidays');
        Schema::dropIfExists('payroll_missions');
        Schema::dropIfExists('payroll_leave_requests');
        Schema::dropIfExists('payroll_leave_types');
        Schema::dropIfExists('payroll_attendance_records');
        Schema::dropIfExists('payroll_time_punches');
        Schema::dropIfExists('payroll_attendance_schedules');
        Schema::dropIfExists('payroll_attendance_shifts');
    }
};
