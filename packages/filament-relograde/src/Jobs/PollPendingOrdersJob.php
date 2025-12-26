<?php

namespace Haida\FilamentRelograde\Jobs;

use Haida\FilamentRelograde\Clients\RelogradeClientFactory;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Models\RelogradeOrder;
use Haida\FilamentRelograde\Services\RelogradeOrderSynchronizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PollPendingOrdersJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $connectionId,
        public int $olderThanMinutes = 5,
        public int $limit = 50,
    ) {}

    public function handle(RelogradeClientFactory $clientFactory, RelogradeOrderSynchronizer $synchronizer): void
    {
        $connection = RelogradeConnection::find($this->connectionId);
        if (! $connection) {
            return;
        }

        $threshold = now()->subMinutes($this->olderThanMinutes);
        $orders = RelogradeOrder::query()
            ->where('connection_id', $connection->getKey())
            ->where('order_status', 'pending')
            ->where('updated_at', '<=', $threshold)
            ->limit($this->limit)
            ->get();

        if ($orders->isEmpty()) {
            return;
        }

        $client = $clientFactory->make($connection);

        foreach ($orders as $order) {
            $payload = $client->findOrder($order->trx);
            $synchronizer->sync($connection, $payload);
        }
    }
}
