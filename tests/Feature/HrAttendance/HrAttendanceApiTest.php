<?php

namespace Tests\Feature\HrAttendance;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\EmployeeConsent;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\Timesheet;
use Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1\EmployeeConsentController;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class HrAttendanceApiTest extends HrAttendanceTestCase
{
    /**
     * @return array{company: AccountingCompany, branch: AccountingBranch, employee: PayrollEmployee}
     */
    private function createCompanyBranchEmployee($tenant): array
    {
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create([
            'name' => 'Company '.$tenant->getKey(),
        ]);

        $branch = AccountingBranch::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'Branch '.$tenant->getKey(),
        ]);

        $employee = PayrollEmployee::query()->create([
            'company_id' => $company->getKey(),
            'branch_id' => $branch->getKey(),
            'first_name' => 'Ali',
            'last_name' => 'Tester',
            'marital_status' => 'single',
            'children_count' => 0,
        ]);

        return compact('company', 'branch', 'employee');
    }

    public function test_timesheet_generate_requires_permission(): void
    {
        $tenant = $this->createTenant('Tenant Timesheet Deny');
        $data = $this->createCompanyBranchEmployee($tenant);

        $user = $this->createUserWithPermissions($tenant, []);

        Sanctum::actingAs($user, [
            'tenant:'.$tenant->getKey(),
        ]);

        $response = $this->postJson('/api/v1/payroll-attendance/timesheets/generate', [
            'company_id' => $data['company']->getKey(),
            'branch_id' => $data['branch']->getKey(),
            'period_start' => Carbon::now()->startOfMonth()->toDateString(),
            'period_end' => Carbon::now()->endOfMonth()->toDateString(),
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $response->assertForbidden();
    }

    public function test_timesheet_generate_creates_timesheet(): void
    {
        $tenant = $this->createTenant('Tenant Timesheet');
        $data = $this->createCompanyBranchEmployee($tenant);

        $user = $this->createUserWithPermissions($tenant, [
            'payroll.timesheet.manage',
            'payroll.timesheet.view',
        ]);

        Sanctum::actingAs($user, [
            'payroll.timesheet.manage',
            'payroll.timesheet.view',
            'tenant:'.$tenant->getKey(),
        ]);

        $periodStart = Carbon::now()->startOfMonth()->toDateString();
        $periodEnd = Carbon::now()->endOfMonth()->toDateString();

        $response = $this->postJson('/api/v1/payroll-attendance/timesheets/generate', [
            'company_id' => $data['company']->getKey(),
            'branch_id' => $data['branch']->getKey(),
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $response->assertOk();

        TenantContext::setTenant($tenant);

        $this->assertTrue(
            Timesheet::query()
                ->where('company_id', $data['company']->getKey())
                ->where('employee_id', $data['employee']->getKey())
                ->whereDate('period_start', $periodStart)
                ->whereDate('period_end', $periodEnd)
                ->exists()
        );
    }

    public function test_biometric_consent_disabled_rejects_store(): void
    {
        config()->set('filament-payroll-attendance-ir.privacy.biometric_enabled', false);

        $tenant = $this->createTenant('Tenant Consent');
        $data = $this->createCompanyBranchEmployee($tenant);

        $user = $this->createUserWithPermissions($tenant, [
            'payroll.consent.manage',
        ]);

        Sanctum::actingAs($user, [
            'payroll.consent.manage',
            'tenant:'.$tenant->getKey(),
        ]);

        $response = $this->postJson('/api/v1/payroll-attendance/employee-consents', [
            'company_id' => $data['company']->getKey(),
            'branch_id' => $data['branch']->getKey(),
            'employee_id' => $data['employee']->getKey(),
            'consent_type' => 'biometric_verification',
            'is_granted' => true,
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $response->assertStatus(422);
    }

    public function test_employee_consent_show_logs_sensitive_access(): void
    {
        config()->set('filament-payroll-attendance-ir.privacy.require_access_reason', false);

        $tenant = $this->createTenant('Tenant Consent Log');
        $data = $this->createCompanyBranchEmployee($tenant);

        $user = $this->createUserWithPermissions($tenant, [
            'payroll.consent.view',
        ]);

        Sanctum::actingAs($user, [
            'payroll.consent.view',
            'tenant:'.$tenant->getKey(),
        ]);

        TenantContext::setTenant($tenant);
        $consent = EmployeeConsent::query()->create([
            'tenant_id' => $tenant->getKey(),
            'company_id' => $data['company']->getKey(),
            'branch_id' => $data['branch']->getKey(),
            'employee_id' => $data['employee']->getKey(),
            'consent_type' => 'location_tracking',
            'is_granted' => true,
            'granted_by' => $user->getKey(),
            'granted_at' => now(),
        ]);

        TenantContext::setTenant($tenant);

        $request = Request::create(
            '/api/v1/payroll-attendance/employee-consents/'.$consent->getKey(),
            'GET'
        );
        $request->headers->set('X-Access-Reason', 'audit');
        $this->app->instance('request', $request);

        $this->actingAs($user);

        $resource = app(EmployeeConsentController::class)->show($consent);

        $this->assertSame($consent->getKey(), $resource->resource->getKey());

        $this->assertDatabaseHas('payroll_sensitive_access_logs', [
            'tenant_id' => $tenant->getKey(),
            'actor_id' => $user->getKey(),
            'subject_type' => EmployeeConsent::class,
            'subject_id' => $consent->getKey(),
            'reason' => 'audit',
        ]);
    }

    public function test_employee_consent_is_tenant_scoped(): void
    {
        $tenantA = $this->createTenant('Tenant A');
        $tenantB = $this->createTenant('Tenant B');

        $dataB = $this->createCompanyBranchEmployee($tenantB);

        $user = $this->createUserWithPermissions($tenantA, [
            'payroll.consent.view',
        ]);

        Sanctum::actingAs($user, [
            'payroll.consent.view',
            'tenant:'.$tenantA->getKey(),
        ]);

        TenantContext::setTenant($tenantB);
        $consent = EmployeeConsent::query()->create([
            'tenant_id' => $tenantB->getKey(),
            'company_id' => $dataB['company']->getKey(),
            'branch_id' => $dataB['branch']->getKey(),
            'employee_id' => $dataB['employee']->getKey(),
            'consent_type' => 'location_tracking',
            'is_granted' => true,
            'granted_by' => $user->getKey(),
            'granted_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/payroll-attendance/employee-consents/'.$consent->getKey(), [
            'X-Tenant-ID' => $tenantA->getKey(),
        ]);

        $response->assertNotFound();
    }
}
