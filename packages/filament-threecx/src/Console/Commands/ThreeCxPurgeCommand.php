<?php

namespace Haida\FilamentThreeCx\Console\Commands;

use Haida\FilamentThreeCx\Models\ThreeCxApiAuditLog;
use Haida\FilamentThreeCx\Models\ThreeCxCallLog;
use Haida\FilamentThreeCx\Models\ThreeCxSyncCursor;
use Illuminate\Console\Command;

class ThreeCxPurgeCommand extends Command
{
    protected $signature = 'threecx:purge';

    protected $description = 'Purge 3CX data based on retention settings.';

    public function handle(): int
    {
        $callDays = (int) config('filament-threecx.retention.call_logs_days', 180);
        $auditDays = (int) config('filament-threecx.retention.api_audit_days', 90);
        $syncDays = (int) config('filament-threecx.retention.sync_cursor_days', 365);

        $callCount = 0;
        if ($callDays > 0) {
            $callCount = ThreeCxCallLog::query()
                ->where('created_at', '<', now()->subDays($callDays))
                ->delete();
        }

        $auditCount = 0;
        if ($auditDays > 0) {
            $auditCount = ThreeCxApiAuditLog::query()
                ->where('created_at', '<', now()->subDays($auditDays))
                ->delete();
        }

        $cursorCount = 0;
        if ($syncDays > 0) {
            $cursorCount = ThreeCxSyncCursor::query()
                ->where('last_synced_at', '<', now()->subDays($syncDays))
                ->delete();
        }

        $this->info("پاک‌سازی انجام شد. تماس‌ها: {$callCount} | ممیزی: {$auditCount} | سینک: {$cursorCount}");

        return self::SUCCESS;
    }
}
