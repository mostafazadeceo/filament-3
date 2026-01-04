<?php

namespace Tests\Feature\PettyCash;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentPettyCashIr\Application\Services\PettyCashAiService;
use Haida\FilamentPettyCashIr\Models\PettyCashAiSuggestion;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\PettyCash\Concerns\CreatesPettyCashFixtures;
use Tests\TestCase;

class PettyCashAiSuggestionTest extends TestCase
{
    use RefreshDatabase;
    use CreatesPettyCashFixtures;

    public function test_ai_suggestion_requires_permission(): void
    {
        config(['filament-petty-cash-ir.ai.enabled' => true]);

        $tenant = $this->createTenant('Tenant A');
        $company = $this->createCompany($tenant);
        $fund = $this->createFund($tenant, $company);
        $category = $this->createCategory($tenant, $company);
        $expense = $this->createExpense($tenant, $company, $fund, $category);

        $user = $this->createUser(false);
        $this->actingAs($user);

        $result = app(PettyCashAiService::class)->suggestExpense($expense);

        $this->assertFalse($result['enabled'] ?? true);
        $this->assertSame(0, PettyCashAiSuggestion::query()->count());
    }

    public function test_ai_suggestion_acceptance_and_rejection_are_logged(): void
    {
        config(['filament-petty-cash-ir.ai.enabled' => true]);

        $tenant = $this->createTenant('Tenant B');
        $company = $this->createCompany($tenant);
        $fund = $this->createFund($tenant, $company);
        $category = $this->createCategory($tenant, $company);
        $expense = $this->createExpense($tenant, $company, $fund, $category, [
            'description' => null,
        ]);

        $user = $this->createUser(true);
        $this->actingAs($user);

        $service = app(PettyCashAiService::class);
        $result = $service->suggestExpense($expense);

        $this->assertTrue($result['enabled'] ?? false);
        $this->assertSame(1, PettyCashAiSuggestion::query()->count());

        $suggestion = $service->applyExpenseSuggestion($expense, $user->getKey());
        $this->assertNotNull($suggestion);
        $this->assertSame('accepted', $suggestion->status);
        $this->assertSame($user->getKey(), $suggestion->decided_by);
        $this->assertNotNull($suggestion->decided_at);

        $expense->refresh();
        $this->assertNotNull($expense->description);

        $service->suggestExpense($expense);
        $rejected = $service->rejectExpenseSuggestion($expense, $user->getKey());
        $this->assertNotNull($rejected);
        $this->assertSame('rejected', $rejected->status);
    }

    public function test_ai_suggestions_are_tenant_scoped(): void
    {
        config(['filament-petty-cash-ir.ai.enabled' => true]);

        $tenantA = $this->createTenant('Tenant C');
        $companyA = $this->createCompany($tenantA);
        $fundA = $this->createFund($tenantA, $companyA);
        $expenseA = $this->createExpense($tenantA, $companyA, $fundA);

        $user = $this->createUser(true);
        $this->actingAs($user);

        app(PettyCashAiService::class)->suggestExpense($expenseA);

        $tenantB = $this->createTenant('Tenant D');
        $companyB = $this->createCompany($tenantB);
        $fundB = $this->createFund($tenantB, $companyB);
        $expenseB = $this->createExpense($tenantB, $companyB, $fundB);

        app(PettyCashAiService::class)->suggestExpense($expenseB);

        TenantContext::setTenant($tenantA);
        $this->assertSame(1, PettyCashAiSuggestion::query()->count());

        TenantContext::setTenant($tenantB);
        $this->assertSame(1, PettyCashAiSuggestion::query()->count());
    }
}
