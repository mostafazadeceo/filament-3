<?php

namespace Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Application\Services\AiReportService;
use Vendor\FilamentPayrollAttendanceIr\Application\Services\AttendancePolicyEngine;
use Vendor\FilamentPayrollAttendanceIr\Application\UseCases\RecalculateWorktime;
use Vendor\FilamentPayrollAttendanceIr\Application\UseCases\GenerateTimesheets;
use Vendor\FilamentPayrollAttendanceIr\Application\UseCases\ResolveException;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendanceException;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendancePolicy;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\TimeEvent;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAiLog;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceRecord;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceSchedule;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceShift;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTimePunch;
use App\Models\User;

class HrAttendancePolicyTest extends \Tests\TestCase
{
    use RefreshDatabase;

    public function test_tenant_isolation_for_time_events(): void
    {
        $tenantA = Tenant::query()->create(['name' => 'Tenant A', 'slug' => 'tenant-a']);
        $tenantB = Tenant::query()->create(['name' => 'Tenant B', 'slug' => 'tenant-b']);

        TenantContext::setTenant($tenantA);
        $companyA = AccountingCompany::query()->create(['name' => 'Company A']);
        $employeeA = PayrollEmployee::query()->create([
            'company_id' => $companyA->getKey(),
            'first_name' => 'Ali',
            'last_name' => 'TenantA',
            'marital_status' => 'single',
            'children_count' => 0,
        ]);

        TimeEvent::query()->create([
            'company_id' => $companyA->getKey(),
            'employee_id' => $employeeA->getKey(),
            'event_at' => now(),
            'event_type' => 'clock_in',
            'source' => 'manual',
        ]);

        TenantContext::setTenant($tenantB);

        $this->assertSame(0, TimeEvent::query()->count());
    }

    public function test_policy_engine_creates_exception_for_missing_wifi(): void
    {
        $tenant = Tenant::query()->create(['name' => 'Tenant', 'slug' => 'tenant-policy']);
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create(['name' => 'Policy Company']);
        $employee = PayrollEmployee::query()->create([
            'company_id' => $company->getKey(),
            'first_name' => 'Sara',
            'last_name' => 'Policy',
            'marital_status' => 'single',
            'children_count' => 0,
        ]);

        AttendancePolicy::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'Wifi Policy',
            'status' => 'active',
            'is_default' => true,
            'rules' => [
                'require_wifi' => true,
            ],
        ]);

        $event = TimeEvent::query()->create([
            'company_id' => $company->getKey(),
            'employee_id' => $employee->getKey(),
            'event_at' => now(),
            'event_type' => 'clock_in',
            'source' => 'manual',
        ]);

        app(AttendancePolicyEngine::class)->evaluateTimeEvent($event);

        $this->assertTrue(
            AttendanceException::query()->where('type', 'wifi_required')->exists()
        );
    }

    public function test_generate_timesheets_overtime_cap_creates_exception(): void
    {
        $tenant = Tenant::query()->create(['name' => 'Tenant', 'slug' => 'tenant-timesheet']);
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create(['name' => 'Timesheet Company']);
        $branch = AccountingBranch::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'Main Branch',
        ]);
        $employee = PayrollEmployee::query()->create([
            'company_id' => $company->getKey(),
            'branch_id' => $branch->getKey(),
            'first_name' => 'Reza',
            'last_name' => 'Overtime',
            'marital_status' => 'single',
            'children_count' => 0,
        ]);

        AttendancePolicy::query()->create([
            'company_id' => $company->getKey(),
            'branch_id' => $branch->getKey(),
            'name' => 'Overtime Policy',
            'status' => 'active',
            'is_default' => true,
            'rules' => [
                'max_overtime_minutes' => 30,
            ],
        ]);

        PayrollAttendanceRecord::query()->create([
            'company_id' => $company->getKey(),
            'branch_id' => $branch->getKey(),
            'employee_id' => $employee->getKey(),
            'work_date' => now()->toDateString(),
            'worked_minutes' => 480,
            'overtime_minutes' => 60,
            'status' => 'approved',
        ]);

        app(GenerateTimesheets::class)->execute(
            $company->getKey(),
            $branch->getKey(),
            now()->startOfMonth(),
            now()->endOfMonth()
        );

        $this->assertTrue(
            AttendanceException::query()->where('type', 'overtime_cap_exceeded')->exists()
        );
    }

    public function test_attendance_calculator_respects_grace_minutes(): void
    {
        $tenant = Tenant::query()->create(['name' => 'Tenant', 'slug' => 'tenant-grace']);
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create(['name' => 'Grace Company']);
        $employee = PayrollEmployee::query()->create([
            'company_id' => $company->getKey(),
            'first_name' => 'Mehrdad',
            'last_name' => 'Grace',
            'marital_status' => 'single',
            'children_count' => 0,
        ]);

        AttendancePolicy::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'Grace Policy',
            'status' => 'active',
            'is_default' => true,
            'rules' => [
                'late_grace_minutes' => 10,
            ],
        ]);

        $shift = PayrollAttendanceShift::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'Shift',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'break_minutes' => 0,
            'is_active' => true,
        ]);

        $schedule = PayrollAttendanceSchedule::query()->create([
            'company_id' => $company->getKey(),
            'employee_id' => $employee->getKey(),
            'shift_id' => $shift->getKey(),
            'work_date' => now()->toDateString(),
            'status' => 'scheduled',
        ]);

        PayrollTimePunch::query()->create([
            'company_id' => $company->getKey(),
            'employee_id' => $employee->getKey(),
            'punch_at' => now()->setTime(8, 5),
            'type' => 'in',
        ]);

        PayrollTimePunch::query()->create([
            'company_id' => $company->getKey(),
            'employee_id' => $employee->getKey(),
            'punch_at' => now()->setTime(16, 0),
            'type' => 'out',
        ]);

        $record = app(RecalculateWorktime::class)->execute($schedule);

        $this->assertSame(0, $record->late_minutes);
    }

    public function test_recalculate_worktime_is_idempotent(): void
    {
        $tenant = Tenant::query()->create(['name' => 'Tenant', 'slug' => 'tenant-idempotent']);
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create(['name' => 'Idempotent Company']);
        $employee = PayrollEmployee::query()->create([
            'company_id' => $company->getKey(),
            'first_name' => 'Hamid',
            'last_name' => 'Idempotent',
            'marital_status' => 'single',
            'children_count' => 0,
        ]);

        $shift = PayrollAttendanceShift::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'Shift',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'break_minutes' => 0,
            'is_active' => true,
        ]);

        $schedule = PayrollAttendanceSchedule::query()->create([
            'company_id' => $company->getKey(),
            'employee_id' => $employee->getKey(),
            'shift_id' => $shift->getKey(),
            'work_date' => now()->toDateString(),
            'status' => 'scheduled',
        ]);

        PayrollTimePunch::query()->create([
            'company_id' => $company->getKey(),
            'employee_id' => $employee->getKey(),
            'punch_at' => now()->setTime(9, 0),
            'type' => 'in',
        ]);

        PayrollTimePunch::query()->create([
            'company_id' => $company->getKey(),
            'employee_id' => $employee->getKey(),
            'punch_at' => now()->setTime(17, 0),
            'type' => 'out',
        ]);

        $first = app(RecalculateWorktime::class)->execute($schedule);
        $second = app(RecalculateWorktime::class)->execute($schedule);

        $this->assertSame($first->getKey(), $second->getKey());
        $this->assertSame(1, PayrollAttendanceRecord::query()->count());
    }

    public function test_resolve_exception_requires_notes(): void
    {
        $tenant = Tenant::query()->create(['name' => 'Tenant', 'slug' => 'tenant-exception']);
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create(['name' => 'Exception Company']);
        $employee = PayrollEmployee::query()->create([
            'company_id' => $company->getKey(),
            'first_name' => 'Niloofar',
            'last_name' => 'Exception',
            'marital_status' => 'single',
            'children_count' => 0,
        ]);

        $exception = AttendanceException::query()->create([
            'company_id' => $company->getKey(),
            'employee_id' => $employee->getKey(),
            'type' => 'manual_edit_without_reason',
            'severity' => 'low',
            'status' => 'open',
            'detected_at' => now(),
        ]);

        $this->expectException(HttpException::class);
        app(ResolveException::class)->execute($exception, []);
    }

    public function test_ai_report_fake_provider_is_deterministic(): void
    {
        config()->set('filament-payroll-attendance-ir.ai.enabled', true);
        config()->set('filament-payroll-attendance-ir.ai.provider', \Vendor\FilamentPayrollAttendanceIr\Infrastructure\Ai\FakeAiProvider::class);

        $tenant = Tenant::query()->create(['name' => 'Tenant', 'slug' => 'tenant-ai']);
        TenantContext::setTenant($tenant);

        $user = User::query()->create([
            'name' => 'AI Admin',
            'email' => 'ai-admin@example.test',
            'password' => bcrypt('password'),
        ]);
        $user->is_super_admin = true;
        $user->save();
        $this->actingAs($user);

        $company = AccountingCompany::query()->create(['name' => 'AI Company']);

        $result = app(AiReportService::class)->generatePersianManagerReport([
            'company_id' => $company->getKey(),
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
        ]);

        $this->assertTrue($result['enabled'] ?? false);
        $this->assertStringContainsString('گزارش مدیریتی آزمایشی', $result['report'] ?? '');
        $this->assertSame(1, PayrollAiLog::query()->count());
    }

    public function test_ai_report_requires_permission(): void
    {
        config()->set('filament-payroll-attendance-ir.ai.enabled', true);

        $tenant = Tenant::query()->create(['name' => 'Tenant', 'slug' => 'tenant-ai-deny']);
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create(['name' => 'AI Company Denied']);

        $result = app(AiReportService::class)->generatePersianManagerReport([
            'company_id' => $company->getKey(),
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
        ]);

        $this->assertFalse($result['enabled'] ?? true);
    }
}
