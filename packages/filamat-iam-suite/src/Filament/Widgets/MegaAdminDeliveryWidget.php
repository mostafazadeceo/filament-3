<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\Notification;
use Filamat\IamSuite\Models\WebhookDelivery;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MegaAdminDeliveryWidget extends StatsOverviewWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'notification.view';

    protected ?string $heading = 'تحویل پیام و وبهوک';

    protected ?string $description = '۲۴ ساعت اخیر';

    protected int|string|array $columnSpan = 1;

    protected int|array|null $columns = ['@xl' => 2, '!@lg' => 2];

    protected function getStats(): array
    {
        $since = now()->subDay();

        $queuedNotifications = Notification::query()
            ->where('status', 'queued')
            ->where('created_at', '>=', $since)
            ->count();

        $failedNotifications = Notification::query()
            ->where('status', 'failed')
            ->where('created_at', '>=', $since)
            ->count();

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
            Stat::make('اعلان در صف', number_format($queuedNotifications))
                ->icon('heroicon-o-queue-list'),
            Stat::make('اعلان ناموفق', number_format($failedNotifications))
                ->icon('heroicon-o-x-circle'),
            Stat::make('وبهوک ناموفق', number_format($webhookFailed))
                ->icon('heroicon-o-exclamation-triangle'),
            Stat::make('سلامت وبهوک', $webhookHealth)
                ->icon('heroicon-o-heart'),
        ];
    }
}
