<?php

namespace Vendor\FilamentPayrollAttendanceIr\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAdvance;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollContract;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLoanInstallment;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollRun;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollSlip;

class PayrollRunService
{
    public function __construct(
        private readonly PayrollCalculatorService $calculator,
        private readonly PayrollAuditService $auditService,
    ) {}

    public function generate(PayrollRun $run): void
    {
        DB::transaction(function () use ($run): void {
            $employees = $this->employeesWithContracts($run->company_id, $run->branch_id);

            foreach ($employees as $employee) {
                $this->generateForEmployee($run, $employee, 'official');
                $this->generateForEmployee($run, $employee, 'internal');
            }

            $this->auditService->log('payroll.run.generated', $run);
        });
    }

    /**
     * @return Collection<int, PayrollEmployee>
     */
    protected function employeesWithContracts(int $companyId, ?int $branchId): Collection
    {
        $query = PayrollEmployee::query()
            ->where('company_id', $companyId)
            ->where('status', 'active');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    protected function generateForEmployee(PayrollRun $run, PayrollEmployee $employee, string $scope): void
    {
        $contract = PayrollContract::query()
            ->where('employee_id', $employee->getKey())
            ->where('company_id', $run->company_id)
            ->where('scope', $scope)
            ->where('status', 'active')
            ->where('effective_from', '<=', $run->period_end)
            ->where(function ($query) use ($run): void {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $run->period_start);
            })
            ->orderByDesc('effective_from')
            ->first();

        if (! $contract) {
            return;
        }

        $result = $this->calculator->calculate($run, $employee, $contract);

        $items = $result['items'];
        $this->appendLoanDeductions($run, $employee, $items);
        $this->appendAdvanceDeductions($run, $employee, $items);

        $slip = PayrollSlip::query()->updateOrCreate(
            [
                'payroll_run_id' => $run->getKey(),
                'employee_id' => $employee->getKey(),
                'scope' => $scope,
            ],
            [
                'tenant_id' => $run->tenant_id,
                'company_id' => $run->company_id,
                'branch_id' => $run->branch_id,
                'status' => 'draft',
                'gross_amount' => $result['slip']['gross_amount'],
                'deductions_amount' => $result['slip']['deductions_amount'],
                'net_amount' => $result['slip']['net_amount'],
                'insurance_employee_amount' => $result['slip']['insurance_employee_amount'],
                'insurance_employer_amount' => $result['slip']['insurance_employer_amount'],
                'tax_amount' => $result['slip']['tax_amount'],
                'issued_at' => now(),
            ]
        );

        $slip->items()->delete();
        foreach ($items as $item) {
            $slip->items()->create([
                'tenant_id' => $run->tenant_id,
                'company_id' => $run->company_id,
                'code' => $item['code'] ?? null,
                'name' => $item['name'] ?? null,
                'type' => $item['type'] ?? 'earning',
                'amount' => $item['amount'] ?? 0,
                'tax_method' => $item['tax_method'] ?? 'progressive',
                'tax_rate' => $item['tax_rate'] ?? null,
                'is_insurable' => (bool) ($item['is_insurable'] ?? false),
                'is_recurring' => (bool) ($item['is_recurring'] ?? false),
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    protected function appendLoanDeductions(PayrollRun $run, PayrollEmployee $employee, array &$items): void
    {
        $installments = PayrollLoanInstallment::query()
            ->whereHas('loan', function ($query) use ($employee): void {
                $query->where('employee_id', $employee->getKey())
                    ->where('status', 'active');
            })
            ->whereNull('paid_at')
            ->whereBetween('due_date', [$run->period_start, $run->period_end])
            ->get();

        foreach ($installments as $installment) {
            $items[] = [
                'code' => 'LOAN',
                'name' => 'قسط وام',
                'type' => 'deduction',
                'amount' => (float) $installment->amount,
                'tax_method' => 'none',
                'is_insurable' => false,
                'metadata' => ['installment_id' => $installment->getKey()],
            ];
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    protected function appendAdvanceDeductions(PayrollRun $run, PayrollEmployee $employee, array &$items): void
    {
        $advances = PayrollAdvance::query()
            ->where('employee_id', $employee->getKey())
            ->where('status', 'open')
            ->whereBetween('advance_date', [$run->period_start, $run->period_end])
            ->get();

        foreach ($advances as $advance) {
            $items[] = [
                'code' => 'ADV',
                'name' => 'مساعده',
                'type' => 'deduction',
                'amount' => (float) $advance->amount,
                'tax_method' => 'none',
                'is_insurable' => false,
                'metadata' => ['advance_id' => $advance->getKey()],
            ];
        }
    }
}
