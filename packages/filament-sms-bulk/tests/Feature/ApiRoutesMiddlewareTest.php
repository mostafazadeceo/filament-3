<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Tests\Feature;

use Haida\SmsBulk\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class ApiRoutesMiddlewareTest extends TestCase
{
    public function test_campaign_submit_route_has_scope_middleware(): void
    {
        $route = collect(Route::getRoutes()->getRoutes())
            ->first(fn ($item) => $item->uri() === 'api/v1/sms-bulk/campaigns/{id}/submit' && in_array('POST', $item->methods(), true));

        $this->assertNotNull($route);

        $middleware = $route->middleware();
        $this->assertTrue(collect($middleware)->contains(fn ($m) => str_contains((string) $m, 'filamat-iam.scope:sms-bulk.campaign.submit')));
    }
}
