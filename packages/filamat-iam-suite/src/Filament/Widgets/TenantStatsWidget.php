<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\SecurityEvent;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\Wallet;
use Filamat\IamSuite\Models\WalletTransaction;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantStatsWidget extends StatsOverviewWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'iam.view';

    protected function getStats(): array
    {
        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            return [
                Stat::make('اعضا', '0'),
                Stat::make('کیف پول', '0'),
                Stat::make('اشتراک فعال', '0'),
                Stat::make('دعوت‌های باز', '0'),
            ];
        }

        $members = $tenant->users()->count();
        $pendingInvites = $tenant->users()->wherePivot('status', 'invited')->count();
        $activeSubscriptions = Subscription::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('status', 'active')
            ->count();
        $walletBalance = Wallet::query()->where('tenant_id', $tenant->getKey())->sum('balance');
        $walletVolume = (float) WalletTransaction::query()
            ->whereHas('wallet', fn ($query) => $query->where('tenant_id', $tenant->getKey()))
            ->where('created_at', '>=', now()->subMonth())
            ->sum('amount');
        $securityEvents = SecurityEvent::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('occurred_at', '>=', now()->subDay())
            ->count();

        return [
            Stat::make('اعضا', (string) $members),
            Stat::make('موجودی کیف پول', number_format((float) $walletBalance, 2)),
            Stat::make('اشتراک فعال', (string) $activeSubscriptions),
            Stat::make('دعوت‌های باز', (string) $pendingInvites),
            Stat::make('حجم تراکنش (۳۰روز)', number_format($walletVolume, 2)),
            Stat::make('رویدادهای امنیتی (۲۴ساعت)', (string) $securityEvents),
        ];
    }
}
