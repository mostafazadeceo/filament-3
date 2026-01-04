<?php

namespace Haida\FilamentThreeCx\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Haida\FilamentThreeCx\Models\ThreeCxCallLog;
use Illuminate\Support\Facades\Schema;

class ThreeCxCallOverviewWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'وضعیت تماس‌ها';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        if (! $this->hasCallLogTable()) {
            return $this->emptyStats();
        }

        $today = now()->startOfDay();

        $callsToday = ThreeCxCallLog::query()
            ->where('started_at', '>=', $today)
            ->count();

        $missedCalls = ThreeCxCallLog::query()
            ->where('started_at', '>=', $today)
            ->whereIn('status', ['missed', 'no_answer'])
            ->count();

        $avgDuration = ThreeCxCallLog::query()
            ->whereNotNull('duration')
            ->avg('duration');

        $avgLabel = $avgDuration ? round((float) $avgDuration).' ثانیه' : '-';

        return [
            Stat::make('تماس‌های امروز', (string) $callsToday),
            Stat::make('بی‌پاسخ', (string) $missedCalls),
            Stat::make('میانگین مدت', $avgLabel),
        ];
    }

    private function hasCallLogTable(): bool
    {
        try {
            return Schema::hasTable('threecx_call_logs');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return array<Stat>
     */
    private function emptyStats(): array
    {
        return [
            Stat::make('تماس‌های امروز', '-'),
            Stat::make('بی‌پاسخ', '-'),
            Stat::make('میانگین مدت', '-'),
        ];
    }
}
