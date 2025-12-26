<?php

namespace Haida\FilamentRelograde\Jobs;

use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Services\RelogradeAlertService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckLowBalanceAlertsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public ?int $connectionId = null,
    ) {}

    public function handle(RelogradeAlertService $alertService): void
    {
        if ($this->connectionId) {
            $connection = RelogradeConnection::find($this->connectionId);
            if ($connection) {
                $alertService->checkLowBalances($connection);
            }

            return;
        }

        $alertService->checkLowBalances();
    }
}
