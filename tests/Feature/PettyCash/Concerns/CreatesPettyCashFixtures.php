<?php

namespace Tests\Feature\PettyCash\Concerns;

use App\Models\User;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentPettyCashIr\Models\PettyCashCategory;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

trait CreatesPettyCashFixtures
{
    protected function createTenant(string $name = 'Tenant'): Tenant
    {
        $tenant = Tenant::query()->create([
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)),
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        return $tenant;
    }

    protected function createCompany(Tenant $tenant): AccountingCompany
    {
        return AccountingCompany::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Company '.$tenant->getKey(),
        ]);
    }

    protected function createFund(Tenant $tenant, AccountingCompany $company): PettyCashFund
    {
        return PettyCashFund::query()->create([
            'tenant_id' => $tenant->getKey(),
            'company_id' => $company->getKey(),
            'name' => 'Fund '.$company->getKey(),
            'status' => 'active',
            'currency' => 'IRR',
            'opening_balance' => 1000000,
            'current_balance' => 1000000,
        ]);
    }

    protected function createCategory(Tenant $tenant, AccountingCompany $company): PettyCashCategory
    {
        return PettyCashCategory::query()->create([
            'tenant_id' => $tenant->getKey(),
            'company_id' => $company->getKey(),
            'name' => 'Supplies',
            'status' => 'active',
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    protected function createExpense(
        Tenant $tenant,
        AccountingCompany $company,
        PettyCashFund $fund,
        ?PettyCashCategory $category = null,
        array $overrides = []
    ): PettyCashExpense {
        return PettyCashExpense::query()->create(array_merge([
            'tenant_id' => $tenant->getKey(),
            'company_id' => $company->getKey(),
            'fund_id' => $fund->getKey(),
            'category_id' => $category?->getKey(),
            'expense_date' => now()->toDateString(),
            'amount' => 5000000,
            'currency' => 'IRR',
            'status' => 'draft',
            'description' => 'Expense for test',
            'receipt_required' => true,
        ], $overrides));
    }

    protected function createUser(bool $superAdmin = false): User
    {
        return User::factory()->create([
            'is_super_admin' => $superAdmin,
        ]);
    }
}
