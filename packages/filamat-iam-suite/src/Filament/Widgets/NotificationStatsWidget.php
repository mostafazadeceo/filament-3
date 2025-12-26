<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\Notification;
use Filamat\IamSuite\Models\SecurityEvent;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NotificationStatsWidget extends StatsOverviewWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'notification.view';

    protected function getStats(): array
    {
        $tenantId = TenantContext::getTenantId();

        $query = Notification::query();
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $since = now()->subDay();
        $sent = (clone $query)->where('status', 'sent')->where('created_at', '>=', $since)->count();
        $queued = (clone $query)->where('status', 'queued')->where('created_at', '>=', $since)->count();
        $failed = (clone $query)->where('status', 'failed')->where('created_at', '>=', $since)->count();

        $otpSuccess = SecurityEvent::query()
            ->when($tenantId, fn ($builder) => $builder->where('tenant_id', $tenantId))
            ->where('type', 'otp.verified')
            ->where('occurred_at', '>=', $since)
            ->count();

        return [
            Stat::make('ارسال‌شده (۲۴ساعت)', (string) $sent),
            Stat::make('در صف (۲۴ساعت)', (string) $queued),
            Stat::make('ناموفق (۲۴ساعت)', (string) $failed),
            Stat::make('رمز یکبارمصرف موفق (۲۴ساعت)', (string) $otpSuccess),
        ];
    }
}
