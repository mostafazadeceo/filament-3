<?php

declare(strict_types=1);

namespace Tests\Feature\ProvidersCore;

use Haida\ProvidersCore\Contracts\ProviderAdapter;
use Haida\ProvidersCore\DataTransferObjects\ProviderContext;
use Haida\ProvidersCore\DataTransferObjects\ProviderResult;
use Haida\ProvidersCore\Services\ProviderJobDispatcher;
use Haida\ProvidersCore\Services\ProviderRegistry;
use Haida\ProvidersCore\Support\ProviderAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderJobDispatcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatch_sync_executes_job_and_updates_log(): void
    {
        $registry = app(ProviderRegistry::class);
        $registry->register('dummy', DummyDispatcherAdapter::class);

        $dispatcher = app(ProviderJobDispatcher::class);
        $context = new ProviderContext(null, null, false);

        $log = $dispatcher->dispatchSync(ProviderAction::SyncProducts, $context, 'dummy');

        $log->refresh();

        $this->assertSame('succeeded', $log->status);
        $this->assertSame(1, $log->attempts);
        $this->assertSame(2, $log->result['data']['synced']);
    }
}

class DummyDispatcherAdapter implements ProviderAdapter
{
    public function key(): string
    {
        return 'dummy';
    }

    public function label(): string
    {
        return 'Dummy';
    }

    public function supportsSandbox(): bool
    {
        return false;
    }

    public function syncProducts(ProviderContext $context, array $payload = []): ProviderResult
    {
        return ProviderResult::ok(['synced' => 2]);
    }

    public function syncInventory(ProviderContext $context, array $payload = []): ProviderResult
    {
        return ProviderResult::fail('Not supported');
    }

    public function createOrder(ProviderContext $context, array $payload): ProviderResult
    {
        return ProviderResult::fail('Not supported');
    }

    public function fulfillOrder(ProviderContext $context, array $payload): ProviderResult
    {
        return ProviderResult::fail('Not supported');
    }

    public function fetchOrderStatus(ProviderContext $context, array $payload): ProviderResult
    {
        return ProviderResult::fail('Not supported');
    }

    public function handleWebhook(ProviderContext $context, array $payload): ProviderResult
    {
        return ProviderResult::fail('Not supported');
    }
}
