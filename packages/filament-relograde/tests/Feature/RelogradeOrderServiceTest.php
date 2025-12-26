<?php

namespace Haida\FilamentRelograde\Tests\Feature;

use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Models\RelogradeOrder;
use Haida\FilamentRelograde\Models\RelogradeOrderItem;
use Haida\FilamentRelograde\Services\RelogradeOrderService;
use Haida\FilamentRelograde\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class RelogradeOrderServiceTest extends TestCase
{
    public function test_create_confirm_find_flow(): void
    {
        Http::preventStrayRequests();

        $connection = RelogradeConnection::create([
            'name' => 'Default',
            'environment' => 'sandbox',
            'api_key' => 'token',
            'is_default' => true,
        ]);

        Http::fake([
            'https://connect.relograde.com/api/1.02/order' => Http::response([
                'data' => [
                    'trx' => 'trx-1',
                    'orderStatus' => 'created',
                    'items' => [],
                ],
            ], 200),
            'https://connect.relograde.com/api/1.02/order/confirm/trx-1' => Http::response([
                'data' => [
                    'trx' => 'trx-1',
                    'orderStatus' => 'finished',
                    'items' => [],
                ],
            ], 200),
        ]);

        $service = app(RelogradeOrderService::class);
        $order = $service->createOrder($connection, [
            'paymentCurrency' => 'USD',
            'items' => [
                ['productSlug' => 'test', 'amount' => 1],
            ],
        ], 'confirm');

        $this->assertSame('trx-1', $order->trx);
        $this->assertSame('finished', $order->order_status);
    }

    public function test_resolve_single_item_enforced(): void
    {
        $connection = RelogradeConnection::create([
            'name' => 'Default',
            'environment' => 'sandbox',
            'api_key' => 'token',
            'is_default' => true,
        ]);

        $order = RelogradeOrder::create([
            'connection_id' => $connection->getKey(),
            'trx' => 'trx-2',
            'order_status' => 'created',
        ]);

        RelogradeOrderItem::create([
            'order_id' => $order->getKey(),
            'product_slug' => 'p1',
            'amount' => 1,
            'raw_json' => [],
        ]);
        RelogradeOrderItem::create([
            'order_id' => $order->getKey(),
            'product_slug' => 'p2',
            'amount' => 1,
            'raw_json' => [],
        ]);

        $service = app(RelogradeOrderService::class);

        $this->expectException(\RuntimeException::class);
        $service->resolveOrder($order);
    }

    public function test_cancel_204_marks_deleted(): void
    {
        Http::preventStrayRequests();

        $connection = RelogradeConnection::create([
            'name' => 'Default',
            'environment' => 'sandbox',
            'api_key' => 'token',
            'is_default' => true,
        ]);

        $order = RelogradeOrder::create([
            'connection_id' => $connection->getKey(),
            'trx' => 'trx-3',
            'order_status' => 'created',
        ]);

        Http::fake([
            'https://connect.relograde.com/api/1.02/order/cancel/trx-3' => Http::response(null, 204),
        ]);

        $service = app(RelogradeOrderService::class);
        $updated = $service->cancelOrder($order);

        $this->assertSame('deleted', $updated->order_status);
    }
}
