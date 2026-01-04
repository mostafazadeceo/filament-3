<?php

namespace Haida\FilamentThreeCx\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Models\ThreeCxSyncCursor;
use Illuminate\Support\Facades\Schema;

class ThreeCxSyncStatusWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'وضعیت همگام‌سازی';

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        if (! $this->hasSyncTables()) {
            return $this->emptyStats();
        }

        try {
            $lastSync = ThreeCxSyncCursor::query()->max('last_synced_at');
            $lastHealth = ThreeCxInstance::query()->max('last_health_at');
        } catch (\Throwable) {
            return $this->emptyStats();
        }

        return [
            Stat::make('آخرین همگام‌سازی', $this->formatTimestamp($lastSync)),
            Stat::make('آخرین سلامت', $this->formatTimestamp($lastHealth)),
        ];
    }

    private function formatTimestamp(mixed $value): string
    {
        if (blank($value)) {
            return '-';
        }

        return Carbon::parse($value)->locale('fa')->diffForHumans();
    }

    private function hasSyncTables(): bool
    {
        try {
            return Schema::hasTable('threecx_sync_cursors')
                && Schema::hasTable('threecx_instances');
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
            Stat::make('آخرین همگام‌سازی', '-'),
            Stat::make('آخرین سلامت', '-'),
        ];
    }
}
