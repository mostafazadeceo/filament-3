<?php

namespace Tests\Feature\PettyCash;

use Haida\FilamentPettyCashIr\Application\UseCases\Expense\SubmitExpense;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\Feature\PettyCash\Concerns\CreatesPettyCashFixtures;
use Tests\TestCase;

class PettyCashWorkflowTest extends TestCase
{
    use RefreshDatabase;
    use CreatesPettyCashFixtures;

    public function test_submit_expense_sets_workflow_fields(): void
    {
        $tenant = $this->createTenant('Tenant Flow');
        $company = $this->createCompany($tenant);
        $fund = $this->createFund($tenant, $company);
        $expense = $this->createExpense($tenant, $company, $fund);

        $user = $this->createUser(true);
        $this->actingAs($user);

        $expense = app(SubmitExpense::class)->handle($expense, $user->getKey());

        $this->assertSame(PettyCashStatuses::EXPENSE_SUBMITTED, $expense->status);
        $this->assertSame(1, $expense->approval_steps_required);
        $this->assertSame(0, $expense->approval_steps_completed);
        $this->assertSame([], $expense->approval_history);
    }

    public function test_submit_expense_requires_draft_status(): void
    {
        $tenant = $this->createTenant('Tenant Flow 2');
        $company = $this->createCompany($tenant);
        $fund = $this->createFund($tenant, $company);
        $expense = $this->createExpense($tenant, $company, $fund, null, [
            'status' => PettyCashStatuses::EXPENSE_SUBMITTED,
        ]);

        $user = $this->createUser(true);
        $this->actingAs($user);

        $this->expectException(ValidationException::class);

        app(SubmitExpense::class)->handle($expense, $user->getKey());
    }
}
