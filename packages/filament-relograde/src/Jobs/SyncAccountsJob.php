<?php

namespace Haida\FilamentRelograde\Jobs;

use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Services\RelogradeSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncAccountsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $connectionId,
    ) {}

    public function handle(RelogradeSyncService $syncService): void
    {
        $connection = RelogradeConnection::find($this->connectionId);
        if (! $connection) {
            return;
        }

        $syncService->syncAccounts($connection);
    }
}
