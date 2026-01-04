<?php

declare(strict_types=1);

namespace Tests\Feature\Providers;

use Filamat\IamSuite\Models\Tenant;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Haida\ProvidersEsimGoCore\Services\EsimGoCatalogueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EsimGoCatalogueServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalogue_sync_creates_products(): void
    {
        Http::fake([
            'https://api.esim-go.com/v2.5/catalogue*' => Http::response([
                'data' => [
                    [
                        'name' => 'EU-1GB',
                        'description' => 'EU bundle',
                        'groups' => ['EU'],
                        'countries' => ['DE', 'FR'],
                        'dataAmount' => 1024,
                        'duration' => 7,
                        'price' => 9.5,
                        'currency' => 'USD',
                        'billingType' => 'FixedCost',
                    ],
                ],
                'pagination' => [
                    'totalPages' => 1,
                ],
            ], 200),
        ]);

        $tenant = Tenant::query()->create([
            'name' => 'Tenant A',
            'slug' => 'tenant-a',
            'status' => 'active',
        ]);

        $connection = EsimGoConnection::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Default',
            'api_key' => 'secret',
            'status' => 'active',
        ]);

        $count = app(EsimGoCatalogueService::class)->sync($connection, [], true);

        $this->assertSame(1, $count);
        $this->assertDatabaseHas('esim_go_products', [
            'tenant_id' => $tenant->getKey(),
            'bundle_name' => 'EU-1GB',
        ]);
    }

    public function test_catalogue_sync_supports_bundles_response(): void
    {
        Http::fake([
            'https://api.esim-go.com/v2.5/catalogue*' => Http::response([
                'bundles' => [
                    [
                        'name' => 'esim_1GB_7D_AD_V2',
                        'description' => 'Andorra bundle',
                        'countries' => [
                            ['name' => 'Andorra', 'region' => 'Europe', 'iso' => 'AD'],
                        ],
                        'dataAmount' => 1000,
                        'duration' => 7,
                        'price' => 6.9,
                        'currency' => 'USD',
                        'billingType' => 'FixedCost',
                    ],
                ],
                'pageCount' => 1,
                'pageSize' => 1,
                'rows' => 1,
            ], 200),
        ]);

        $tenant = Tenant::query()->create([
            'name' => 'Tenant B',
            'slug' => 'tenant-b',
            'status' => 'active',
        ]);

        $connection = EsimGoConnection::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Default',
            'api_key' => 'secret',
            'status' => 'active',
        ]);

        $count = app(EsimGoCatalogueService::class)->sync($connection, [], true);

        $this->assertSame(1, $count);
        $this->assertDatabaseHas('esim_go_products', [
            'tenant_id' => $tenant->getKey(),
            'bundle_name' => 'esim_1GB_7D_AD_V2',
        ]);
    }
}
