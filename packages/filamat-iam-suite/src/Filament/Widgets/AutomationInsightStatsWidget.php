<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\IamAiReport;
use Filamat\IamSuite\Models\WebhookDelivery;
use Filamat\IamSuite\Services\Automation\N8nApiClient;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AutomationInsightStatsWidget extends StatsOverviewWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'automation.reports.view';

    protected function getStats(): array
    {
        $since = now()->subDay();
        $tenantId = TenantContext::getTenantId();

        $deliveryQuery = WebhookDelivery::query()
            ->where('created_at', '>=', $since)
            ->whereHas('webhook', function ($builder) use ($tenantId) {
                $builder->where('type', 'automation');
                if ($tenantId) {
                    $builder->where('tenant_id', $tenantId);
                }
            });

        $delivered = (clone $deliveryQuery)->where('status', 'delivered')->count();
        $failed = (clone $deliveryQuery)->where('status', 'failed')->count();
        $health = ($delivered + $failed) > 0
            ? round(($delivered / max(1, $delivered + $failed)) * 100).'%'
            : 'N/A';

        $latestReportQuery = IamAiReport::query();
        if ($tenantId) {
            $latestReportQuery->where('tenant_id', $tenantId);
        }
        $latestReport = $latestReportQuery->latest()->first();

        $riskCountQuery = IamAiReport::query()
            ->whereIn('severity', ['high', 'critical'])
            ->where('created_at', '>=', now()->subDays(7));

        if ($tenantId) {
            $riskCountQuery->where('tenant_id', $tenantId);
        }

        $stats = [
            Stat::make('سلامت اتصال n8n (۲۴ساعت)', $health),
            Stat::make('آخرین گزارش هوش', $latestReport?->severity ?? 'N/A'),
            Stat::make('ریسک‌های ۷ روز اخیر', (string) $riskCountQuery->count()),
        ];

        $apiHealth = app(N8nApiClient::class)->health();
        if ($apiHealth['enabled']) {
            $label = $apiHealth['status'] === 'ok' ? 'OK' : $apiHealth['status'];
            $stats[] = Stat::make('سلامت API n8n', $label);
        }

        return $stats;
    }
}
