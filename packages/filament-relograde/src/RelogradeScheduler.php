<?php

namespace Haida\FilamentRelograde;

use Haida\FilamentRelograde\Jobs\CheckLowBalanceAlertsJob;
use Haida\FilamentRelograde\Jobs\PollPendingOrdersJob;
use Haida\FilamentRelograde\Jobs\SyncAccountsJob;
use Haida\FilamentRelograde\Jobs\SyncBrandsJob;
use Haida\FilamentRelograde\Jobs\SyncProductsJob;
use Haida\FilamentRelograde\Models\RelogradeConnection;

class RelogradeScheduler
{
    public static function syncAccounts(): void
    {
        RelogradeConnection::query()->each(function (RelogradeConnection $connection) {
            SyncAccountsJob::dispatch($connection->getKey());
        });
    }

    public static function syncCatalog(): void
    {
        RelogradeConnection::query()->each(function (RelogradeConnection $connection) {
            SyncBrandsJob::dispatch($connection->getKey(), true);
            SyncProductsJob::dispatch($connection->getKey(), true);
        });
    }

    public static function pollPendingOrders(): void
    {
        $olderThan = (int) config('relograde.polling.older_than_minutes', 5);
        $limit = (int) config('relograde.polling.limit', 50);

        RelogradeConnection::query()->each(function (RelogradeConnection $connection) use ($olderThan, $limit) {
            PollPendingOrdersJob::dispatch($connection->getKey(), $olderThan, $limit);
        });
    }

    public static function checkLowBalanceAlerts(): void
    {
        RelogradeConnection::query()->each(function (RelogradeConnection $connection) {
            CheckLowBalanceAlertsJob::dispatch($connection->getKey());
        });
    }
}
