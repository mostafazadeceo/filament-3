<?php

namespace Vendor\FilamentPayrollAttendanceIr\Services;

use Carbon\Carbon;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollContract;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollRun;

class PayrollCalculatorService
{
    public function __construct(
        private readonly PayrollSettingsResolver $resolver,
        private readonly AttendanceSummaryService $attendanceSummaryService,
    ) {}

    /**
     * @return array{slip: array<string, mixed>, items: array<int, array<string, mixed>>}
     */
    public function calculate(PayrollRun $run, PayrollEmployee $employee, PayrollContract $contract): array
    {
        $periodEnd = Carbon::parse($run->period_end);
        $periodStart = Carbon::parse($run->period_start);
        $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;
        $monthlyHours = (float) ($contract->monthly_hours ?: config('filament-payroll-attendance-ir.attendance.default_monthly_hours', 176));
        $hourlyRate = $monthlyHours > 0 ? ((float) $contract->base_salary / $monthlyHours) : 0;

        $summary = $this->attendanceSummaryService->summarize($employee->getKey(), $periodStart, $periodEnd);

        $items = [];

        $this->addItem($items, [
            'code' => 'BASE',
            'name' => 'حقوق پایه',
            'type' => 'earning',
            'amount' => (float) $contract->base_salary,
            'tax_method' => 'progressive',
            'is_insurable' => true,
        ]);

        $allowanceTable = $this->resolver->resolveAllowanceTable($employee->company_id, $periodEnd);

        $housingAllowance = (float) ($contract->housing_allowance ?: ($allowanceTable?->housing_allowance ?? 0));
        $this->addItem($items, [
            'code' => 'HOUSING',
            'name' => 'حق مسکن',
            'type' => 'earning',
            'amount' => $housingAllowance,
            'tax_method' => 'progressive',
            'is_insurable' => true,
        ]);

        $foodAllowance = (float) ($contract->food_allowance ?: ($allowanceTable?->food_allowance ?? 0));
        $this->addItem($items, [
            'code' => 'FOOD',
            'name' => 'بن',
            'type' => 'earning',
            'amount' => $foodAllowance,
            'tax_method' => 'progressive',
            'is_insurable' => true,
        ]);

        if ($employee->marital_status === 'married') {
            $marriageAllowance = (float) ($contract->marriage_allowance ?: ($allowanceTable?->marriage_allowance ?? 0));
            $this->addItem($items, [
                'code' => 'MARRIAGE',
                'name' => 'حق تأهل',
                'type' => 'earning',
                'amount' => $marriageAllowance,
                'tax_method' => 'progressive',
                'is_insurable' => true,
            ]);
        }

        if ($employee->children_count > 0) {
            $childDaily = (float) ($contract->child_allowance ?: ($allowanceTable?->child_allowance_daily ?? 0));
            $childAmount = $childDaily * $daysInPeriod * (int) $employee->children_count;
            $this->addItem($items, [
                'code' => 'CHILD',
                'name' => 'حق اولاد',
                'type' => 'earning',
                'amount' => $childAmount,
                'tax_method' => 'flat',
                'tax_rate' => $this->resolveFlatTaxRate($employee->company_id, $periodEnd),
                'is_insurable' => false,
            ]);
        }

        $seniorityDaily = (float) ($contract->seniority_allowance ?: ($allowanceTable?->seniority_allowance_daily ?? 0));
        if ($this->hasSeniority($employee, $periodEnd) && $seniorityDaily > 0) {
            $this->addItem($items, [
                'code' => 'SENIORITY',
                'name' => 'پایه سنوات',
                'type' => 'earning',
                'amount' => $seniorityDaily * $daysInPeriod,
                'tax_method' => 'progressive',
                'is_insurable' => true,
            ]);
        }

        $overtimeAmount = $this->minutesToAmount($summary['overtime_minutes'], $hourlyRate, config('filament-payroll-attendance-ir.payroll.overtime_factor', 1.4));
        $this->addItem($items, [
            'code' => 'OT',
            'name' => 'اضافه‌کار',
            'type' => 'earning',
            'amount' => $overtimeAmount,
            'tax_method' => 'flat',
            'tax_rate' => $this->resolveFlatTaxRate($employee->company_id, $periodEnd),
            'is_insurable' => true,
        ]);

        $nightAmount = $this->minutesToAmount($summary['night_minutes'], $hourlyRate, config('filament-payroll-attendance-ir.payroll.night_factor', 0.35));
        $this->addItem($items, [
            'code' => 'NIGHT',
            'name' => 'شب‌کاری',
            'type' => 'earning',
            'amount' => $nightAmount,
            'tax_method' => 'flat',
            'tax_rate' => $this->resolveFlatTaxRate($employee->company_id, $periodEnd),
            'is_insurable' => true,
        ]);

        $fridayAmount = $this->minutesToAmount($summary['friday_minutes'], $hourlyRate, config('filament-payroll-attendance-ir.payroll.friday_factor', 0.4));
        $this->addItem($items, [
            'code' => 'FRIDAY',
            'name' => 'جمعه‌کاری',
            'type' => 'earning',
            'amount' => $fridayAmount,
            'tax_method' => 'flat',
            'tax_rate' => $this->resolveFlatTaxRate($employee->company_id, $periodEnd),
            'is_insurable' => true,
        ]);

        $absenceDeduction = $this->minutesToAmount($summary['absence_minutes'], $hourlyRate, 1);
        $this->addItem($items, [
            'code' => 'ABSENCE',
            'name' => 'کسر غیبت',
            'type' => 'deduction',
            'amount' => $absenceDeduction,
            'tax_method' => 'none',
            'is_insurable' => false,
        ]);

        $lateDeduction = $this->minutesToAmount($summary['late_minutes'] + $summary['early_leave_minutes'], $hourlyRate, 1);
        $this->addItem($items, [
            'code' => 'LATE',
            'name' => 'کسر تأخیر',
            'type' => 'deduction',
            'amount' => $lateDeduction,
            'tax_method' => 'none',
            'is_insurable' => false,
        ]);

        $gross = $this->sumItems($items, 'earning');

        $insurance = $contract->insurance_included
            ? $this->calculateInsurance($employee->company_id, $periodEnd, $items)
            : ['employee' => 0.0, 'employer' => 0.0];

        $tax = $contract->tax_included
            ? $this->calculateTax($employee->company_id, $periodEnd, $items)
            : 0.0;

        $this->addItem($items, [
            'code' => 'INS_EMP',
            'name' => 'بیمه سهم کارمند',
            'type' => 'deduction',
            'amount' => $insurance['employee'],
            'tax_method' => 'none',
            'is_insurable' => false,
        ]);

        $this->addItem($items, [
            'code' => 'TAX',
            'name' => 'مالیات حقوق',
            'type' => 'deduction',
            'amount' => $tax,
            'tax_method' => 'none',
            'is_insurable' => false,
        ]);

        $deductions = $this->sumItems($items, 'deduction');
        $net = $gross - $deductions;

        return [
            'slip' => [
                'gross_amount' => $gross,
                'deductions_amount' => $deductions,
                'net_amount' => $net,
                'insurance_employee_amount' => $insurance['employee'],
                'insurance_employer_amount' => $insurance['employer'],
                'tax_amount' => $tax,
            ],
            'items' => $items,
        ];
    }

    private function minutesToAmount(int $minutes, float $hourlyRate, float $factor): float
    {
        if ($minutes <= 0 || $hourlyRate <= 0) {
            return 0.0;
        }

        $hours = $minutes / 60;

        return $hours * $hourlyRate * $factor;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function sumItems(array $items, string $type): float
    {
        $sum = 0.0;

        foreach ($items as $item) {
            if (($item['type'] ?? null) !== $type) {
                continue;
            }
            $sum += (float) ($item['amount'] ?? 0);
        }

        return $sum;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array{employee: float, employer: float}
     */
    private function calculateInsurance(int $companyId, Carbon $date, array $items): array
    {
        $table = $this->resolver->resolveInsuranceTable($companyId, $date);
        if (! $table) {
            return ['employee' => 0.0, 'employer' => 0.0];
        }

        $base = 0.0;
        foreach ($items as $item) {
            if (($item['type'] ?? null) !== 'earning') {
                continue;
            }
            if (! ($item['is_insurable'] ?? false)) {
                continue;
            }
            $base += (float) ($item['amount'] ?? 0);
        }

        $cap = $table->max_insurable_monthly ?: null;
        if ($cap && $base > $cap) {
            $base = (float) $cap;
        }

        $employeeRate = ((float) $table->employee_rate) / 100;
        $employerRate = ((float) $table->employer_rate) / 100;

        return [
            'employee' => $base * $employeeRate,
            'employer' => $base * $employerRate,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function calculateTax(int $companyId, Carbon $date, array $items): float
    {
        $table = $this->resolver->resolveTaxTable($companyId, $date);
        if (! $table) {
            return 0.0;
        }

        $progressiveBase = 0.0;
        $flatBase = 0.0;
        $flatRate = $this->resolveFlatTaxRate($companyId, $date);

        foreach ($items as $item) {
            if (($item['type'] ?? null) !== 'earning') {
                continue;
            }

            $taxMethod = $item['tax_method'] ?? 'progressive';
            $amount = (float) ($item['amount'] ?? 0);

            if ($taxMethod === 'flat') {
                $flatBase += $amount * (float) (($item['tax_rate'] ?? $flatRate) ?: $flatRate);

                continue;
            }

            if ($taxMethod === 'progressive') {
                $progressiveBase += $amount;
            }
        }

        $taxable = max(0, $progressiveBase - (float) $table->exemption_amount);
        $progressiveTax = 0.0;

        foreach ($table->brackets as $bracket) {
            $min = (float) $bracket->min_amount;
            $max = $bracket->max_amount ? (float) $bracket->max_amount : null;
            if ($taxable <= $min) {
                continue;
            }
            $upper = $max ?? $taxable;
            $portion = min($taxable, $upper) - $min;
            if ($portion > 0) {
                $progressiveTax += $portion * ((float) $bracket->rate / 100);
            }
            if ($max && $taxable <= $max) {
                break;
            }
        }

        return $progressiveTax + $flatBase;
    }

    private function resolveFlatTaxRate(int $companyId, Carbon $date): float
    {
        $table = $this->resolver->resolveTaxTable($companyId, $date);
        if ($table) {
            return ((float) $table->flat_allowance_rate) / 100;
        }

        return (float) config('filament-payroll-attendance-ir.payroll.flat_allowance_tax_rate', 0.1);
    }

    private function hasSeniority(PayrollEmployee $employee, Carbon $date): bool
    {
        if (! $employee->employment_date) {
            return false;
        }

        return Carbon::parse($employee->employment_date)->diffInDays($date) >= 365;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<string, mixed>  $item
     */
    private function addItem(array &$items, array $item): void
    {
        $amount = (float) ($item['amount'] ?? 0);
        if ($amount <= 0) {
            return;
        }

        $items[] = array_merge([
            'tax_method' => 'progressive',
            'tax_rate' => null,
            'is_insurable' => false,
            'is_recurring' => false,
        ], $item);
    }
}
