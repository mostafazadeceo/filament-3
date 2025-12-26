<?php

namespace Haida\FilamentRelograde\Widgets;

use Filament\Widgets\ChartWidget;
use Haida\FilamentRelograde\Models\RelogradeOrder;
use Haida\FilamentRelograde\Support\RelogradeLabels;
use Haida\FilamentRelograde\Widgets\Concerns\RelogradeWidgetVisibility;

class RelogradeOrdersStatusWidget extends ChartWidget
{
    use RelogradeWidgetVisibility;

    protected ?string $heading = 'وضعیت سفارش‌ها';

    protected function getData(): array
    {
        $days = (int) ($this->filter ?? 7);
        $from = now()->subDays($days);

        $counts = RelogradeOrder::query()
            ->where('date_created', '>=', $from)
            ->selectRaw('order_status, count(*) as total')
            ->groupBy('order_status')
            ->pluck('total', 'order_status')
            ->toArray();

        $statusKeys = ['created', 'pending', 'finished', 'cancelled', 'deleted'];
        $labels = array_map(fn ($key) => RelogradeLabels::orderStatus($key), $statusKeys);
        $data = array_map(fn ($key) => (int) ($counts[$key] ?? 0), $statusKeys);

        return [
            'datasets' => [
                [
                    'label' => 'سفارش‌ها',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            7 => '۷ روز گذشته',
            30 => '۳۰ روز گذشته',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
