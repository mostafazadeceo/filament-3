<?php

namespace Tests\Feature\CommerceCatalog;

use Haida\CommerceCatalog\Services\CatalogPricingService;
use Haida\FilamentCurrencyRates\Models\CurrencyRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogPricingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_convert_from_irr_to_usd(): void
    {
        CurrencyRate::query()->create([
            'code' => 'USD',
            'name' => 'دلار',
            'buy_price' => 500000,
            'sell_price' => 520000,
            'source' => 'test',
            'fetched_at' => now(),
        ]);

        $service = app(CatalogPricingService::class);
        $amount = $service->convert(520000, 'IRR', 'USD');

        $this->assertNotNull($amount);
        $this->assertEqualsWithDelta(1.0, $amount, 0.0001);
    }

    public function test_convert_from_usd_to_irr(): void
    {
        CurrencyRate::query()->create([
            'code' => 'USD',
            'name' => 'دلار',
            'buy_price' => 500000,
            'sell_price' => 520000,
            'source' => 'test',
            'fetched_at' => now(),
        ]);

        $service = app(CatalogPricingService::class);
        $amount = $service->convert(2, 'USD', 'IRR');

        $this->assertNotNull($amount);
        $this->assertEqualsWithDelta(1040000, $amount, 0.0001);
    }
}
