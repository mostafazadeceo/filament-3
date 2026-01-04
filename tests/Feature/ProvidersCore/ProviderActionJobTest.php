<?php

declare(strict_types=1);

namespace Tests\Feature\ProvidersCore;

use Haida\ProvidersCore\Contracts\ProviderAdapter;
use Haida\ProvidersCore\DataTransferObjects\ProviderContext;
use Haida\ProvidersCore\DataTransferObjects\ProviderResult;
use Haida\ProvidersCore\Jobs\ProviderActionJob;
use Haida\ProvidersCore\Models\ProviderJobLog;
use Haida\ProvidersCore\Services\ProviderRegistry;
use Haida\ProvidersCore\Support\ProviderAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderActionJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_updates_log_on_success(): void
    {
        $registry = app(ProviderRegistry::class);
        $registry->register('dummy', DummyProviderAdapter::class);

        $log = ProviderJobLog::query()->create([
            'tenant_id' => null,
            'provider_key' => 'dummy',
            'job_type' => ProviderAction::SyncProducts->value,
            'status' => 'pending',
        ]);

        $job = new ProviderActionJob(
            null,
            'dummy',
            ProviderAction::SyncProducts,
            ['full_sync' => true],
            (int) $log->getKey(),
            null,
            false,
        );

        $job->handle($registry);

        $log->refresh();

        $this->assertSame('succeeded', $log->status);
        $this->assertSame(1, $log->attempts);
        $this->assertSame(3, $log->result['data']['synced']);
    }
}

class DummyProviderAdapter implements ProviderAdapter
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
        return ProviderResult::ok(['synced' => 3]);
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
