<?php

declare(strict_types=1);

namespace Tests\Feature\Pos;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentPos\Models\PosDevice;
use Haida\FilamentPos\Models\PosRegister;
use Haida\FilamentPos\Models\PosSale;
use Haida\FilamentPos\Models\PosStore;
use Haida\FilamentPos\Services\PosCashierSessionService;
use Haida\FilamentPos\Services\PosOutboxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosSyncSimulationTest extends TestCase
{
    use RefreshDatabase;

    public function test_offline_outbox_sale_is_idempotent(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant POS',
            'slug' => 'tenant-pos',
            'status' => 'active',
        ]);
        TenantContext::setTenant($tenant);

        $store = PosStore::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Main Store',
            'status' => 'active',
            'currency' => 'IRR',
        ]);

        $register = PosRegister::query()->create([
            'tenant_id' => $tenant->getKey(),
            'store_id' => $store->getKey(),
            'name' => 'Register 1',
            'status' => 'active',
        ]);

        $device = PosDevice::query()->create([
            'tenant_id' => $tenant->getKey(),
            'register_id' => $register->getKey(),
            'device_uid' => 'DEVICE-1',
            'status' => 'active',
        ]);

        $session = app(PosCashierSessionService::class)->openSession($register, 100, $device->getKey(), null);

        $events = [
            [
                'event_type' => 'sale',
                'idempotency_key' => 'sale-1',
                'payload' => [
                    'store_id' => $store->getKey(),
                    'register_id' => $register->getKey(),
                    'session_id' => $session->getKey(),
                    'items' => [
                        [
                            'name' => 'Item A',
                            'quantity' => 1,
                            'unit_price' => 10000,
                        ],
                    ],
                    'payments' => [
                        [
                            'provider' => 'manual',
                            'amount' => 10000,
                            'status' => 'confirmed',
                        ],
                    ],
                ],
            ],
        ];

        $service = app(PosOutboxService::class);
        $service->processEvents($events, $device, null);
        $service->processEvents($events, $device, null);

        $this->assertSame(1, PosSale::query()->count());
    }
}
