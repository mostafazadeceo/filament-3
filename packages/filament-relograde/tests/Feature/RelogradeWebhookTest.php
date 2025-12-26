<?php

namespace Haida\FilamentRelograde\Tests\Feature;

use Haida\FilamentRelograde\Jobs\ProcessWebhookEventJob;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Models\RelogradeOrder;
use Haida\FilamentRelograde\Models\RelogradeWebhookEvent;
use Haida\FilamentRelograde\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class RelogradeWebhookTest extends TestCase
{
    public function test_webhook_processing_updates_order(): void
    {
        Http::preventStrayRequests();

        $connection = RelogradeConnection::create([
            'name' => 'Default',
            'environment' => 'sandbox',
            'api_key' => 'token',
            'is_default' => true,
        ]);

        $event = RelogradeWebhookEvent::create([
            'connection_id' => $connection->getKey(),
            'event' => 'ORDER_FINISHED',
            'state' => 'sandbox',
            'trx' => 'trx-100',
            'payload' => [
                'event' => 'ORDER_FINISHED',
                'state' => 'sandbox',
                'data' => [
                    'trx' => 'trx-100',
                ],
            ],
            'processing_status' => 'pending',
        ]);

        Http::fake([
            'https://connect.relograde.com/api/1.02/order/trx-100' => Http::response([
                'data' => [
                    'trx' => 'trx-100',
                    'orderStatus' => 'finished',
                    'items' => [
                        [
                            'productSlug' => 'p1',
                            'productName' => 'Product 1',
                            'amount' => 1,
                            'orderLines' => [
                                [
                                    'tag' => 'line-1',
                                    'status' => 'finished',
                                    'voucherCode' => 'CODE1234',
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        (new ProcessWebhookEventJob($event->getKey()))
            ->handle(app(\Haida\FilamentRelograde\Clients\RelogradeClientFactory::class), app(\Haida\FilamentRelograde\Services\RelogradeOrderSynchronizer::class));

        $order = RelogradeOrder::query()->where('trx', 'trx-100')->first();
        $this->assertNotNull($order);
        $this->assertSame('finished', $order->order_status);
        $this->assertSame(1, $order->items()->count());
        $this->assertSame(1, $order->items()->first()->lines()->count());

        $event->refresh();
        $this->assertSame('processed', $event->processing_status);
    }
}
