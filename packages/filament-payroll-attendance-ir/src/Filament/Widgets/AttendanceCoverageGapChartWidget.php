<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceRecord;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceSchedule;

class AttendanceCoverageGapChartWidget extends ChartWidget
{
    protected ?string $heading = 'پوشش شیفت (برنامه‌ریزی در برابر حضور)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $days = (int) ($this->filter ?? 14);
        $from = now()->subDays(max($days - 1, 0))->startOfDay();
        $to = now()->endOfDay();

        $scheduled = PayrollAttendanceSchedule::query()
            ->select([
                'work_date',
                DB::raw('COUNT(*) as scheduled_count'),
            ])
            ->whereBetween('work_date', [$from->toDateString(), $to->toDateString()])
            ->groupBy('work_date')
            ->get()
            ->keyBy(fn ($row) => (string) $row->work_date);

        $attended = PayrollAttendanceRecord::query()
            ->select([
                'work_date',
                DB::raw('COUNT(*) as attended_count'),
            ])
            ->whereBetween('work_date', [$from->toDateString(), $to->toDateString()])
            ->whereIn('status', ['approved', 'locked'])
            ->groupBy('work_date')
            ->get()
            ->keyBy(fn ($row) => (string) $row->work_date);

        $labels = [];
        $scheduledData = [];
        $attendedData = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $from->copy()->addDays($i)->toDateString();
            $labels[] = $date;
            $scheduledData[] = (int) (($scheduled[$date]->scheduled_count ?? 0));
            $attendedData[] = (int) (($attended[$date]->attended_count ?? 0));
        }

        return [
            'datasets' => [
                [
                    'label' => 'برنامه‌ریزی',
                    'data' => $scheduledData,
                    'backgroundColor' => 'rgba(14, 116, 144, 0.7)',
                ],
                [
                    'label' => 'حضور ثبت‌شده',
                    'data' => $attendedData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.7)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            7 => '۷ روز گذشته',
            14 => '۱۴ روز گذشته',
            30 => '۳۰ روز گذشته',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
