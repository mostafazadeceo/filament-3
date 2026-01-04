<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceRecord;

class AttendanceWorkMinutesChartWidget extends ChartWidget
{
    protected ?string $heading = 'روند کارکرد و تأخیر';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $days = (int) ($this->filter ?? 14);
        $from = now()->subDays(max($days - 1, 0))->startOfDay();
        $to = now()->endOfDay();

        $rows = PayrollAttendanceRecord::query()
            ->select([
                'work_date',
                DB::raw('SUM(worked_minutes) as worked_minutes'),
                DB::raw('SUM(late_minutes) as late_minutes'),
            ])
            ->whereBetween('work_date', [$from->toDateString(), $to->toDateString()])
            ->whereIn('status', ['approved', 'locked'])
            ->groupBy('work_date')
            ->orderBy('work_date')
            ->get();

        $byDate = $rows->keyBy(fn ($row) => (string) $row->work_date);

        $labels = [];
        $worked = [];
        $late = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $from->copy()->addDays($i)->toDateString();
            $labels[] = $date;
            $worked[] = (int) (($byDate[$date]->worked_minutes ?? 0));
            $late[] = (int) (($byDate[$date]->late_minutes ?? 0));
        }

        return [
            'datasets' => [
                [
                    'label' => 'کارکرد (دقیقه)',
                    'data' => $worked,
                    'borderColor' => '#2563eb',
                    'backgroundColor' => 'rgba(37, 99, 235, 0.2)',
                ],
                [
                    'label' => 'تأخیر (دقیقه)',
                    'data' => $late,
                    'borderColor' => '#f97316',
                    'backgroundColor' => 'rgba(249, 115, 22, 0.2)',
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
        return 'line';
    }
}
