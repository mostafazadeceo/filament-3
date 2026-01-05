<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\Notification;
use Filament\Widgets\ChartWidget;

class NotificationChannelBreakdownWidget extends ChartWidget
{
    use AuthorizesIam;

    protected static ?string $permission = 'notification.view';

    protected ?string $heading = 'کانال‌های ارسال اعلان';

    protected ?string $description = '۱۴ روز اخیر';

    protected string $color = 'warning';

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $start = now()->subDays(13)->startOfDay();
        $rows = Notification::query()
            ->where('created_at', '>=', $start)
            ->selectRaw("COALESCE(channel, 'unknown') as channel, COUNT(*) as total")
            ->groupBy('channel')
            ->orderByDesc('total')
            ->get();

        $labels = [];
        $data = [];
        $top = $rows->take(6);

        foreach ($top as $row) {
            $labels[] = $this->channelLabel((string) $row->channel);
            $data[] = (int) $row->total;
        }

        if ($rows->count() > $top->count()) {
            $labels[] = 'سایر';
            $data[] = (int) $rows->slice($top->count())->sum('total');
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function channelLabel(string $channel): string
    {
        return match ($channel) {
            'sms' => 'پیامک',
            'email' => 'ایمیل',
            'telegram' => 'تلگرام',
            'whatsapp' => 'واتساپ',
            'webpush' => 'وب‌پوش',
            'bale' => 'بله',
            'unknown' => 'نامشخص',
            default => $channel,
        };
    }
}
