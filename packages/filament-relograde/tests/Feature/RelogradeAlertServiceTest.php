<?php

namespace Haida\FilamentRelograde\Tests\Feature;

use Haida\FilamentRelograde\Models\RelogradeAccount;
use Haida\FilamentRelograde\Models\RelogradeAlert;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Services\RelogradeAlertService;
use Haida\FilamentRelograde\Tests\TestCase;

class RelogradeAlertServiceTest extends TestCase
{
    public function test_low_balance_alerts_created_and_resolved(): void
    {
        $this->app['config']->set('relograde.low_balance_thresholds', [
            'USD' => 100,
        ]);

        $connection = RelogradeConnection::create([
            'name' => 'Default',
            'environment' => 'sandbox',
            'api_key' => 'token',
            'is_default' => true,
        ]);

        $account = RelogradeAccount::create([
            'connection_id' => $connection->getKey(),
            'currency' => 'USD',
            'state' => 'sandbox',
            'total_amount' => 50,
            'raw_json' => [],
        ]);

        $service = app(RelogradeAlertService::class);
        $created = $service->checkLowBalances($connection);

        $this->assertSame(1, $created);
        $alert = RelogradeAlert::query()->where('connection_id', $connection->getKey())->first();
        $this->assertNotNull($alert);
        $this->assertNull($alert->resolved_at);

        $account->update(['total_amount' => 150]);
        $service->checkLowBalances($connection);

        $alert->refresh();
        $this->assertNotNull($alert->resolved_at);
    }
}
