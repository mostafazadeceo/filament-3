<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\WebhookDelivery;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Widgets\ChartWidget;

class WebhookHealthChartWidget extends ChartWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'api.view';

    protected ?string $heading = 'سلامت وبهوک‌ها';

    protected ?string $description = '۱۴ روز اخیر';

    protected string $color = 'info';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $start = now()->subDays(13)->startOfDay();
        $rows = $this->baseQuery()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, status, COUNT(*) as total')
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

        $dates = $this->buildDateKeys($start, 14);
        $statusMap = [
            'delivered' => 'موفق',
            'failed' => 'ناموفق',
        ];

        $bucket = [];
        foreach ($rows as $row) {
            $bucket[$row->date][$row->status] = (int) $row->total;
        }

        $datasets = [];
        foreach ($statusMap as $status => $label) {
            $datasets[] = [
                'label' => $label,
                'data' => array_map(
                    fn (string $date) => (int) ($bucket[$date][$status] ?? 0),
                    $dates
                ),
                'borderWidth' => 2,
                'fill' => false,
                'tension' => 0.25,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $this->buildLabels($dates),
        ];
    }

    private function baseQuery()
    {
        $query = WebhookDelivery::query();
        $tenantId = TenantContext::getTenantId();

        if ($tenantId) {
            $query->whereHas('webhook', function ($builder) use ($tenantId) {
                $builder->where('tenant_id', $tenantId);
            });
        }

        return $query;
    }

    /**
     * @return array<int, string>
     */
    private function buildDateKeys(\Illuminate\Support\Carbon $start, int $days): array
    {
        $dates = [];

        for ($i = 0; $i < $days; $i++) {
            $dates[] = $start->copy()->addDays($i)->toDateString();
        }

        return $dates;
    }

    /**
     * @param  array<int, string>  $dates
     * @return array<int, string>
     */
    private function buildLabels(array $dates): array
    {
        return array_map(
            fn (string $date) => \Illuminate\Support\Carbon::parse($date)->format('m/d'),
            $dates
        );
    }
}
