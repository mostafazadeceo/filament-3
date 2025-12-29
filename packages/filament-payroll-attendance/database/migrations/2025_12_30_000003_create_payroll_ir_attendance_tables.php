<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_ir_shifts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->string('name');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('break_minutes')->default(0);
            $table->boolean('is_overnight')->default(false);
            $table->boolean('is_shift_work')->default(false);
            $table->string('shift_work_type')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'branch_id', 'is_active'], 'pir_shift_company_branch_active_idx');
        });

        Schema::create('payroll_ir_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->foreignId('employee_id')->constrained('payroll_ir_employees')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('payroll_ir_shifts')->cascadeOnDelete();
            $table->date('date');
            $table->string('status')->default('planned');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['employee_id', 'date'], 'pir_schedule_employee_date_unique');
            $table->index(['company_id', 'branch_id', 'date'], 'pir_schedule_company_date_idx');
            $table->index(['shift_id', 'date'], 'pir_schedule_shift_date_idx');
        });

        Schema::create('payroll_ir_punches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->foreignId('employee_id')->constrained('payroll_ir_employees')->cascadeOnDelete();
            $table->dateTime('punch_time');
            $table->string('punch_type')->default('in');
            $table->string('source')->default('manual');
            $table->string('source_ref')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'punch_time'], 'pir_punch_employee_time_idx');
            $table->index(['company_id', 'branch_id', 'punch_time'], 'pir_punch_company_time_idx');
        });

        Schema::create('payroll_ir_attendance_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('accounting_ir_companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('accounting_ir_branches')->nullOnDelete();
            $table->foreignId('employee_id')->constrained('payroll_ir_employees')->cascadeOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('payroll_ir_shifts')->nullOnDelete();
            $table->date('date');
            $table->dateTime('scheduled_start')->nullable();
            $table->dateTime('scheduled_end')->nullable();
            $table->dateTime('actual_start')->nullable();
            $table->dateTime('actual_end')->nullable();
            $table->unsignedInteger('work_minutes')->default(0);
            $table->unsignedInteger('overtime_minutes')->default(0);
            $table->unsignedInteger('night_minutes')->default(0);
            $table->unsignedInteger('friday_minutes')->default(0);
            $table->unsignedInteger('holiday_minutes')->default(0);
            $table->unsignedInteger('late_minutes')->default(0);
            $table->unsignedInteger('early_minutes')->default(0);
            $table->unsignedInteger('absence_minutes')->default(0);
            $table->string('status')->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'date'], 'pir_attendance_employee_date_unique');
            $table->index(['company_id', 'branch_id', 'date'], 'pir_attendance_company_date_idx');
            $table->index(['status', 'date'], 'pir_attendance_status_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_ir_attendance_records');
        Schema::dropIfExists('payroll_ir_punches');
        Schema::dropIfExists('payroll_ir_schedules');
        Schema::dropIfExists('payroll_ir_shifts');
    }
};
