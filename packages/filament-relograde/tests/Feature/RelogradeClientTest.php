<?php

namespace Haida\FilamentRelograde\Tests\Feature;

use Haida\FilamentRelograde\Clients\RelogradeClientFactory;
use Haida\FilamentRelograde\Models\RelogradeApiLog;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class RelogradeClientTest extends TestCase
{
    public function test_list_products_pagination(): void
    {
        Http::preventStrayRequests();

        $connection = RelogradeConnection::create([
            'name' => 'Default',
            'environment' => 'sandbox',
            'api_key' => 'test-token',
            'is_default' => true,
        ]);

        Http::fake(function ($request) {
            $query = [];
            parse_str(parse_url($request->url(), PHP_URL_QUERY) ?? '', $query);
            $page = (int) ($query['page'] ?? 1);

            if ($page === 1) {
                return Http::response([
                    'data' => [
                        ['slug' => 'p1', 'name' => 'Product 1'],
                    ],
                    'pagination' => [
                        'total' => 2,
                        'page' => 1,
                        'limit' => 1,
                        'pages' => 2,
                    ],
                ], 200);
            }

            return Http::response([
                'data' => [
                    ['slug' => 'p2', 'name' => 'Product 2'],
                ],
                'pagination' => [
                    'total' => 2,
                    'page' => 2,
                    'limit' => 1,
                    'pages' => 2,
                ],
            ], 200);
        });

        $client = app(RelogradeClientFactory::class)->make($connection);
        $items = $client->iterateProducts(['limit' => 1])->all();

        $this->assertCount(2, $items);
        $this->assertSame('p1', $items[0]['slug']);
        $this->assertSame('p2', $items[1]['slug']);
    }

    public function test_api_log_redaction(): void
    {
        Http::preventStrayRequests();

        $connection = RelogradeConnection::create([
            'name' => 'Default',
            'environment' => 'sandbox',
            'api_key' => 'secret-token',
            'is_default' => true,
        ]);

        Http::fake([
            'https://connect.relograde.com/api/1.02/account*' => Http::response([
                ['currency' => 'USD', 'state' => 'sandbox', 'totalAmount' => 10],
            ], 200),
        ]);

        $client = app(RelogradeClientFactory::class)->make($connection);
        $client->listAccounts();

        $log = RelogradeApiLog::query()->latest()->first();
        $this->assertNotNull($log);
        $this->assertIsArray($log->request_headers);
        $this->assertArrayNotHasKey('Authorization', $log->request_headers);
    }
}
