<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\WalletTransaction;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Widgets\ChartWidget;

class WalletVolumeChartWidget extends ChartWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'wallet.view';

    protected ?string $heading = 'حجم تراکنش‌ها';

    protected ?string $description = '۱۴ روز اخیر';

    protected string $color = 'primary';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $start = now()->subDays(13)->startOfDay();
        $rows = $this->baseQuery()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        [$labels, $values] = $this->buildSeries($rows, $start, 14);

        return [
            'datasets' => [
                [
                    'label' => 'حجم تراکنش',
                    'data' => $values,
                    'borderWidth' => 2,
                    'fill' => false,
                    'tension' => 0.25,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function baseQuery()
    {
        $query = WalletTransaction::query();
        $tenantId = TenantContext::getTenantId();

        if ($tenantId) {
            $query->whereHas('wallet', function ($builder) use ($tenantId) {
                $builder->where('tenant_id', $tenantId);
            });
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $rows
     * @return array{0: array<int, string>, 1: array<int, float>}
     */
    private function buildSeries(array $rows, \Illuminate\Support\Carbon $start, int $days): array
    {
        $labels = [];
        $values = [];

        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i);
            $key = $day->toDateString();
            $labels[] = $day->format('m/d');
            $values[] = (float) ($rows[$key] ?? 0);
        }

        return [$labels, $values];
    }
}
