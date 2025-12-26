<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\SecurityEvent;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\WalletTransaction;
use Filamat\IamSuite\Models\WebhookDelivery;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SuperAdminStatsWidget extends StatsOverviewWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'iam.view';

    protected function getStats(): array
    {
        $userModel = config('auth.providers.users.model');

        $tenantCount = Tenant::query()->count();
        $userCount = $userModel::query()->count();
        $activeSubscriptions = Subscription::query()->where('status', 'active')->count();
        $mrr = Subscription::query()
            ->with('plan')
            ->get()
            ->sum(fn (Subscription $subscription) => (float) ($subscription->plan?->price ?? 0));

        $since = now()->subDay();
        $securityAlerts = SecurityEvent::query()
            ->whereIn('severity', ['warning', 'critical'])
            ->where('occurred_at', '>=', $since)
            ->count();
        $failedLogins = SecurityEvent::query()
            ->where('type', 'auth.failed')
            ->where('occurred_at', '>=', $since)
            ->count();
        $walletVolume = (float) WalletTransaction::query()
            ->where('created_at', '>=', now()->subMonth())
            ->sum('amount');

        $webhookDelivered = WebhookDelivery::query()
            ->where('status', 'delivered')
            ->where('created_at', '>=', $since)
            ->count();
        $webhookFailed = WebhookDelivery::query()
            ->where('status', 'failed')
            ->where('created_at', '>=', $since)
            ->count();
        $webhookHealth = ($webhookDelivered + $webhookFailed) > 0
            ? round(($webhookDelivered / max(1, $webhookDelivered + $webhookFailed)) * 100).'%'
            : 'N/A';

        return [
            Stat::make('فضاهای کاری', (string) $tenantCount),
            Stat::make('کاربران', (string) $userCount),
            Stat::make('اشتراک‌های فعال', (string) $activeSubscriptions),
            Stat::make('درآمد ماهانه تقریبی', number_format($mrr, 2)),
            Stat::make('هشدارهای امنیتی (۲۴ساعت)', (string) $securityAlerts),
            Stat::make('ورود ناموفق (۲۴ساعت)', (string) $failedLogins),
            Stat::make('حجم تراکنش (۳۰روز)', number_format($walletVolume, 2)),
            Stat::make('سلامت وبهوک (۲۴ساعت)', $webhookHealth),
        ];
    }
}
