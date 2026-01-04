<?php

namespace Tests\Feature\PettyCash;

use Haida\FilamentPettyCashIr\Infrastructure\Idempotency\IdempotencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\PettyCash\Concerns\CreatesPettyCashFixtures;
use Tests\TestCase;

class PettyCashIdempotencyTest extends TestCase
{
    use RefreshDatabase;
    use CreatesPettyCashFixtures;

    public function test_idempotency_prevents_duplicate_callbacks(): void
    {
        $tenant = $this->createTenant('Tenant Idem');
        $company = $this->createCompany($tenant);
        $fund = $this->createFund($tenant, $company);
        $expense = $this->createExpense($tenant, $company, $fund);

        $user = $this->createUser(true);
        $this->actingAs($user);

        $calls = 0;
        $service = app(IdempotencyService::class);

        $service->run('test_action', $expense, 'key-1', $user->getKey(), function () use (&$calls, $expense) {
            $calls++;
            $expense->update(['status' => 'submitted']);

            return $expense->refresh();
        });

        $service->run('test_action', $expense, 'key-1', $user->getKey(), function () use (&$calls) {
            $calls++;

            return null;
        });

        $this->assertSame(1, $calls);
    }
}
