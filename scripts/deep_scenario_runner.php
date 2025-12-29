<?php

declare(strict_types=1);

use App\Models\User;
use Carbon\Carbon;
use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Models\Organization;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\SubscriptionPlan;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\CapabilitySyncService;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentPettyCashIr\Models\PettyCashCategory;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashExpenseAttachment;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;
use Haida\FilamentPettyCashIr\Models\PettyCashSettlementItem;
use Haida\FilamentPettyCashIr\Services\PettyCashPostingService;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Haida\FilamentRestaurantOps\Models\RestaurantGoodsReceipt;
use Haida\FilamentRestaurantOps\Models\RestaurantGoodsReceiptLine;
use Haida\FilamentRestaurantOps\Models\RestaurantItem;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuItem;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuSale;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuSaleLine;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseOrder;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseOrderLine;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseRequest;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseRequestLine;
use Haida\FilamentRestaurantOps\Models\RestaurantRecipe;
use Haida\FilamentRestaurantOps\Models\RestaurantRecipeLine;
use Haida\FilamentRestaurantOps\Models\RestaurantSupplier;
use Haida\FilamentRestaurantOps\Models\RestaurantUom;
use Haida\FilamentRestaurantOps\Models\RestaurantWarehouse;
use Haida\FilamentRestaurantOps\Services\RestaurantGoodsReceiptService;
use Haida\FilamentRestaurantOps\Services\RestaurantMenuSaleService;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\Transition;
use Haida\FilamentWorkhub\Models\Workflow;
use Haida\FilamentWorkhub\Models\WorkType;
use Haida\FilamentWorkhub\Services\WorkflowTransitionService;
use Haida\FilamentWorkhub\Services\WorkItemCreator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Vendor\FilamentAccountingIr\Database\Seeders\AccountingIrSeeder;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\AccountingCompanySetting;
use Vendor\FilamentAccountingIr\Models\AccountPlan;
use Vendor\FilamentAccountingIr\Models\AccountType;
use Vendor\FilamentAccountingIr\Models\ChartAccount;
use Vendor\FilamentAccountingIr\Models\FiscalPeriod;
use Vendor\FilamentAccountingIr\Models\FiscalYear;
use Vendor\FilamentAccountingIr\Models\InventoryDoc;
use Vendor\FilamentAccountingIr\Models\InventoryDocLine;
use Vendor\FilamentAccountingIr\Models\InventoryItem;
use Vendor\FilamentAccountingIr\Models\InventoryWarehouse;
use Vendor\FilamentAccountingIr\Models\Party;
use Vendor\FilamentAccountingIr\Models\ProductService;
use Vendor\FilamentAccountingIr\Models\PurchaseInvoice;
use Vendor\FilamentAccountingIr\Models\PurchaseInvoiceLine;
use Vendor\FilamentAccountingIr\Models\SalesInvoice;
use Vendor\FilamentAccountingIr\Models\SalesInvoiceLine;
use Vendor\FilamentAccountingIr\Models\TaxCategory;
use Vendor\FilamentAccountingIr\Models\TreasuryAccount;
use Vendor\FilamentAccountingIr\Models\Uom;
use Vendor\FilamentAccountingIr\Services\InventoryDocService;
use Vendor\FilamentAccountingIr\Services\PostingService;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAdvance;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAllowanceTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceRecord;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceShift;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollContract;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollInsuranceTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLoan;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLoanInstallment;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollMinimumWageTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollRun;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTaxBracket;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTaxTable;
use Vendor\FilamentPayrollAttendanceIr\Services\PayrollRunService;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

function logLine(string $message): void
{
    echo '['.now()->format('H:i:s').'] '.$message.PHP_EOL;
}

function assertTrue(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}

$runId = now()->format('YmdHis').'-'.Str::upper(Str::random(4));

function ensureAccountType(string $code): AccountType
{
    $type = AccountType::query()->where('code', $code)->first();
    if (! $type) {
        (new AccountingIrSeeder)->run();
        $type = AccountType::query()->where('code', $code)->first();
    }

    if (! $type) {
        throw new RuntimeException('Missing account type: '.$code);
    }

    return $type;
}

function ensureChartAccount(AccountingCompany $company, AccountPlan $plan, AccountType $type, string $code, string $name): ChartAccount
{
    return ChartAccount::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'code' => $code,
    ], [
        'tenant_id' => $company->tenant_id,
        'plan_id' => $plan->getKey(),
        'type_id' => $type->getKey(),
        'name' => $name,
        'level' => 1,
        'is_postable' => true,
        'is_active' => true,
        'sort_order' => 0,
    ]);
}

function ensureFiscalPeriods(FiscalYear $year): void
{
    if ($year->periods()->count() >= 12) {
        return;
    }

    $start = Carbon::parse($year->start_date)->startOfMonth();
    for ($i = 0; $i < 12; $i++) {
        $periodStart = $start->copy()->addMonths($i)->startOfMonth();
        $periodEnd = $start->copy()->addMonths($i)->endOfMonth();
        FiscalPeriod::query()->firstOrCreate([
            'company_id' => $year->company_id,
            'fiscal_year_id' => $year->getKey(),
            'start_date' => $periodStart->toDateString(),
            'end_date' => $periodEnd->toDateString(),
        ], [
            'tenant_id' => $year->tenant_id,
            'name' => $periodStart->format('Y-m'),
            'period_type' => 'month',
            'is_closed' => false,
        ]);
    }
}

function ensureRoleWithPermissions(string $roleName, int $tenantId, array $permissions): Role
{
    $registrar = app(PermissionRegistrar::class);
    $registrar->setPermissionsTeamId($tenantId);

    $role = Role::query()->firstOrCreate([
        'name' => $roleName,
        'guard_name' => 'web',
        'tenant_id' => $tenantId,
    ]);

    $role->syncPermissions($permissions);
    $registrar->forgetCachedPermissions();

    return $role;
}

function ensureUser(string $email, string $name, string $password = 'Secret#1234'): User
{
    $user = User::query()->firstOrCreate([
        'email' => $email,
    ], [
        'name' => $name,
        'password' => $password,
    ]);

    if (! Hash::check($password, $user->password)) {
        $user->password = $password;
        $user->save();
    }

    return $user;
}

function attachUserToTenant(Tenant $tenant, User $user, string $roleName): void
{
    $tenant->users()->syncWithoutDetaching([
        $user->getKey() => [
            'role' => $roleName,
            'status' => 'active',
            'joined_at' => now(),
        ],
    ]);
}

logLine('sync permissions');
app(CapabilitySyncService::class)->sync('web');

$registry = app(CapabilityRegistryInterface::class);
$allPermissions = [];
foreach ($registry->all() as $capability) {
    $allPermissions = array_merge($allPermissions, $capability->permissions);
}
$allPermissions = array_values(array_unique($allPermissions));

$superAdmin = ensureUser('dr.mostafazade@gmail.com', 'Super Admin', 'm@5011700D');
$superAdmin->is_super_admin = true;
$superAdmin->email_verified_at = $superAdmin->email_verified_at ?: now();
$superAdmin->save();

$organization = Organization::query()->firstOrCreate([
    'name' => 'Scenario Organization',
], [
    'owner_user_id' => $superAdmin->getKey(),
    'shared_data_mode' => 'isolated',
]);

$tenants = [];
for ($i = 1; $i <= 2; $i++) {
    $slug = 'workspace-'.$i;
    $tenant = Tenant::query()->firstOrCreate([
        'slug' => $slug,
    ], [
        'name' => 'Workspace '.$i,
        'organization_id' => $organization->getKey(),
        'owner_user_id' => $superAdmin->getKey(),
        'status' => 'active',
        'locale' => 'fa',
        'timezone' => 'Asia/Tehran',
    ]);
    $tenants[] = $tenant;
}

foreach ($tenants as $tenant) {
    logLine('tenant: '.$tenant->slug);
    TenantContext::setTenant($tenant);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());

    $plan = SubscriptionPlan::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'code' => 'scenario-unlimited-'.$tenant->slug,
    ], [
        'scope' => 'tenant',
        'name' => 'Scenario Unlimited '.$tenant->slug,
        'price' => 0,
        'currency' => 'irr',
        'period_days' => 3650,
        'trial_days' => 0,
        'features' => null,
        'is_active' => true,
    ]);

    Subscription::query()->updateOrCreate([
        'tenant_id' => $tenant->getKey(),
        'user_id' => null,
    ], [
        'plan_id' => $plan->getKey(),
        'status' => 'active',
        'provider' => 'scenario',
        'provider_ref' => $tenant->slug.'-scenario',
    ]);

    $roleAdmin = ensureRoleWithPermissions('scenario_admin', $tenant->getKey(), $allPermissions);
    $roleFinance = ensureRoleWithPermissions('finance_manager', $tenant->getKey(), array_values(array_filter($allPermissions, fn ($p) => str_starts_with($p, 'accounting.') || str_starts_with($p, 'petty_cash.'))));
    $roleInventory = ensureRoleWithPermissions('inventory_manager', $tenant->getKey(), array_values(array_filter($allPermissions, fn ($p) => str_starts_with($p, 'restaurant.'))));
    $roleHr = ensureRoleWithPermissions('hr_manager', $tenant->getKey(), array_values(array_filter($allPermissions, fn ($p) => str_starts_with($p, 'payroll.'))));

    attachUserToTenant($tenant, $superAdmin, 'scenario_admin');
    $superAdmin->assignRole($roleAdmin);

    $manager = ensureUser('manager.'.$tenant->slug.'@haida.test', 'Manager '.$tenant->slug);
    $financeUser = ensureUser('finance.'.$tenant->slug.'@haida.test', 'Finance '.$tenant->slug);
    $inventoryUser = ensureUser('inventory.'.$tenant->slug.'@haida.test', 'Inventory '.$tenant->slug);
    $hrUser = ensureUser('hr.'.$tenant->slug.'@haida.test', 'HR '.$tenant->slug);

    attachUserToTenant($tenant, $manager, 'scenario_admin');
    attachUserToTenant($tenant, $financeUser, 'finance_manager');
    attachUserToTenant($tenant, $inventoryUser, 'inventory_manager');
    attachUserToTenant($tenant, $hrUser, 'hr_manager');

    $manager->assignRole($roleAdmin);
    $financeUser->assignRole($roleFinance);
    $inventoryUser->assignRole($roleInventory);
    $hrUser->assignRole($roleHr);

    Auth::login($superAdmin);

    $company = AccountingCompany::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'name' => 'Company '.strtoupper($tenant->slug),
    ], [
        'legal_name' => 'Company '.strtoupper($tenant->slug),
        'timezone' => 'Asia/Tehran',
        'base_currency' => 'IRR',
        'is_active' => true,
    ]);

    $branches = [];
    for ($b = 1; $b <= 2; $b++) {
        $branches[] = AccountingBranch::query()->firstOrCreate([
            'tenant_id' => $tenant->getKey(),
            'company_id' => $company->getKey(),
            'code' => strtoupper($tenant->slug).'-B'.$b,
        ], [
            'name' => 'Branch '.$b.' '.$tenant->slug,
            'is_active' => true,
        ]);
    }

    $fiscalYear = FiscalYear::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'name' => 'FY-'.now()->year,
    ], [
        'tenant_id' => $tenant->getKey(),
        'start_date' => now()->startOfYear()->toDateString(),
        'end_date' => now()->endOfYear()->toDateString(),
        'is_closed' => false,
    ]);
    ensureFiscalPeriods($fiscalYear);

    $plan = AccountPlan::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'name' => 'Default Plan',
    ], [
        'tenant_id' => $tenant->getKey(),
        'industry' => 'general',
        'is_default' => true,
    ]);

    $assetType = ensureAccountType('asset');
    $liabilityType = ensureAccountType('liability');
    $incomeType = ensureAccountType('income');
    $expenseType = ensureAccountType('expense');

    $accCash = ensureChartAccount($company, $plan, $assetType, '1000', 'Cash');
    $accBank = ensureChartAccount($company, $plan, $assetType, '1010', 'Bank');
    $accAr = ensureChartAccount($company, $plan, $assetType, '1100', 'Accounts Receivable');
    $accAp = ensureChartAccount($company, $plan, $liabilityType, '2000', 'Accounts Payable');
    $accSales = ensureChartAccount($company, $plan, $incomeType, '4000', 'Sales Revenue');
    $accSalesTax = ensureChartAccount($company, $plan, $liabilityType, '2100', 'Sales Tax');
    $accPurchase = ensureChartAccount($company, $plan, $expenseType, '5000', 'Purchase Expense');
    $accPurchaseTax = ensureChartAccount($company, $plan, $expenseType, '5100', 'Purchase Tax');
    $accPettyExpense = ensureChartAccount($company, $plan, $expenseType, '5200', 'Petty Cash Expense');

    AccountingCompanySetting::query()->updateOrCreate([
        'company_id' => $company->getKey(),
    ], [
        'tenant_id' => $tenant->getKey(),
        'posting_accounts' => [
            'sales_revenue' => $accSales->getKey(),
            'sales_tax' => $accSalesTax->getKey(),
            'accounts_receivable' => $accAr->getKey(),
            'purchase_expense' => $accPurchase->getKey(),
            'purchase_tax' => $accPurchaseTax->getKey(),
            'accounts_payable' => $accAp->getKey(),
            'cash' => $accCash->getKey(),
            'bank' => $accBank->getKey(),
        ],
        'posting_requires_approval' => false,
        'allow_negative_inventory' => false,
    ]);

    $uomCode = 'UNIT-'.$tenant->slug;
    $uom = Uom::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'code' => $uomCode,
    ], [
        'tenant_id' => $tenant->getKey(),
        'name' => 'Unit',
        'is_default' => true,
    ]);

    $taxCategory = TaxCategory::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'code' => 'VAT',
    ], [
        'tenant_id' => $tenant->getKey(),
        'name' => 'VAT',
        'vat_rate' => 0.09,
        'is_exempt' => false,
    ]);

    $product = ProductService::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'code' => 'PRD-001',
    ], [
        'tenant_id' => $tenant->getKey(),
        'name' => 'Sample Product',
        'item_type' => 'product',
        'uom_id' => $uom->getKey(),
        'tax_category_id' => $taxCategory->getKey(),
        'base_price' => 100000,
        'is_active' => true,
    ]);

    $inventoryItem = InventoryItem::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'product_id' => $product->getKey(),
    ], [
        'tenant_id' => $tenant->getKey(),
        'sku' => 'SKU-'.$tenant->slug,
        'min_stock' => 0,
        'current_stock' => 0,
        'allow_negative' => false,
    ]);

    $warehouses = [];
    foreach ($branches as $branch) {
        $warehouses[] = InventoryWarehouse::query()->firstOrCreate([
            'company_id' => $company->getKey(),
            'branch_id' => $branch->getKey(),
            'code' => 'WH-'.$branch->code,
        ], [
            'tenant_id' => $tenant->getKey(),
            'name' => 'Warehouse '.$branch->code,
            'is_active' => true,
        ]);
    }

    $treasury = TreasuryAccount::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'account_type' => 'bank',
        'name' => 'Main Bank',
    ], [
        'tenant_id' => $tenant->getKey(),
        'account_no' => '123456789',
        'iban' => 'IR000000000000000000000000',
        'bank_name' => 'Sample Bank',
        'currency' => 'IRR',
        'is_active' => true,
    ]);

    $customer = Party::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'party_type' => 'customer',
        'name' => 'Sample Customer',
    ], [
        'tenant_id' => $tenant->getKey(),
        'is_active' => true,
    ]);

    $supplierParty = Party::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'party_type' => 'supplier',
        'name' => 'Sample Supplier',
    ], [
        'tenant_id' => $tenant->getKey(),
        'is_active' => true,
    ]);

    $salesInvoice = SalesInvoice::query()->create([
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'fiscal_year_id' => $fiscalYear->getKey(),
        'party_id' => $customer->getKey(),
        'invoice_no' => 'SI-'.$tenant->slug.'-'.$runId,
        'invoice_date' => now()->toDateString(),
        'status' => 'draft',
        'currency' => 'IRR',
        'subtotal' => 0,
        'discount_total' => 0,
        'tax_total' => 0,
        'total' => 0,
        'is_official' => true,
    ]);

    $qty = 2;
    $unitPrice = 100000;
    $lineSubtotal = $qty * $unitPrice;
    $lineTax = $lineSubtotal * 0.09;
    $lineTotal = $lineSubtotal + $lineTax;

    SalesInvoiceLine::query()->create([
        'sales_invoice_id' => $salesInvoice->getKey(),
        'product_id' => $product->getKey(),
        'description' => 'Sample sale line',
        'quantity' => $qty,
        'unit_price' => $unitPrice,
        'discount_amount' => 0,
        'tax_rate' => 0.09,
        'tax_amount' => $lineTax,
        'line_total' => $lineTotal,
    ]);

    $salesInvoice->update([
        'subtotal' => $lineSubtotal,
        'tax_total' => $lineTax,
        'total' => $lineTotal,
    ]);

    $postingService = app(PostingService::class);
    $salesEntry = $postingService->postSalesInvoice($salesInvoice);
    assertTrue($salesEntry !== null, 'Sales journal entry missing');

    $purchaseInvoice = PurchaseInvoice::query()->create([
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'fiscal_year_id' => $fiscalYear->getKey(),
        'party_id' => $supplierParty->getKey(),
        'invoice_no' => 'PI-'.$tenant->slug.'-'.$runId,
        'invoice_date' => now()->toDateString(),
        'status' => 'draft',
        'currency' => 'IRR',
        'subtotal' => 0,
        'discount_total' => 0,
        'tax_total' => 0,
        'total' => 0,
        'is_official' => true,
    ]);

    PurchaseInvoiceLine::query()->create([
        'purchase_invoice_id' => $purchaseInvoice->getKey(),
        'product_id' => $product->getKey(),
        'description' => 'Sample purchase line',
        'quantity' => 5,
        'unit_price' => 80000,
        'discount_amount' => 0,
        'tax_rate' => 0.09,
        'tax_amount' => 5 * 80000 * 0.09,
        'line_total' => 5 * 80000 * 1.09,
    ]);

    $purchaseInvoice->update([
        'subtotal' => 5 * 80000,
        'tax_total' => 5 * 80000 * 0.09,
        'total' => 5 * 80000 * 1.09,
    ]);

    $purchaseEntry = $postingService->postPurchaseInvoice($purchaseInvoice);
    assertTrue($purchaseEntry !== null, 'Purchase journal entry missing');

    $inventoryDoc = InventoryDoc::query()->create([
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'warehouse_id' => $warehouses[0]->getKey(),
        'doc_type' => 'receipt',
        'doc_no' => 'REC-'.$tenant->slug.'-'.$runId,
        'doc_date' => now()->toDateString(),
        'status' => 'draft',
        'description' => 'Initial stock',
    ]);

    InventoryDocLine::query()->create([
        'inventory_doc_id' => $inventoryDoc->getKey(),
        'inventory_item_id' => $inventoryItem->getKey(),
        'quantity' => 50,
        'unit_cost' => 70000,
    ]);

    $inventoryService = app(InventoryDocService::class);
    $inventoryService->post($inventoryDoc->refresh());

    $issueDoc = InventoryDoc::query()->create([
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'warehouse_id' => $warehouses[0]->getKey(),
        'doc_type' => 'issue',
        'doc_no' => 'ISS-'.$tenant->slug.'-'.$runId,
        'doc_date' => now()->toDateString(),
        'status' => 'draft',
        'description' => 'Issue stock',
    ]);

    InventoryDocLine::query()->create([
        'inventory_doc_id' => $issueDoc->getKey(),
        'inventory_item_id' => $inventoryItem->getKey(),
        'quantity' => 10,
        'unit_cost' => 70000,
    ]);

    $inventoryService->post($issueDoc->refresh());

    $restaurantUom = RestaurantUom::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'name' => 'Kilogram',
    ], [
        'tenant_id' => $tenant->getKey(),
        'symbol' => 'kg',
        'is_base' => true,
    ]);

    $restaurantItem = RestaurantItem::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'name' => 'Rice',
    ], [
        'tenant_id' => $tenant->getKey(),
        'accounting_inventory_item_id' => $inventoryItem->getKey(),
        'code' => 'RICE-'.$tenant->slug,
        'category' => 'raw',
        'is_active' => true,
        'base_uom_id' => $restaurantUom->getKey(),
        'purchase_uom_id' => $restaurantUom->getKey(),
        'consumption_uom_id' => $restaurantUom->getKey(),
        'purchase_to_base_rate' => 1,
        'consumption_to_base_rate' => 1,
        'min_stock' => 1,
        'reorder_point' => 5,
        'track_batch' => true,
        'track_expiry' => true,
    ]);

    $restaurantWarehouses = [];
    foreach ($branches as $branchIndex => $branch) {
        for ($w = 1; $w <= 4; $w++) {
            $restaurantWarehouses[] = RestaurantWarehouse::query()->firstOrCreate([
                'company_id' => $company->getKey(),
                'branch_id' => $branch->getKey(),
                'code' => 'RWH-'.$branch->code.'-'.$w,
            ], [
                'tenant_id' => $tenant->getKey(),
                'accounting_inventory_warehouse_id' => $warehouses[$branchIndex]->getKey(),
                'name' => 'Restaurant WH '.$branch->code.'-'.$w,
                'type' => 'main',
                'is_active' => true,
            ]);
        }
    }

    $restaurantSupplier = RestaurantSupplier::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'name' => 'Supplier '.$tenant->slug,
    ], [
        'tenant_id' => $tenant->getKey(),
        'accounting_party_id' => $supplierParty->getKey(),
        'code' => 'SUP-'.$tenant->slug,
        'status' => 'active',
        'phone' => '021000000',
    ]);

    $purchaseRequest = RestaurantPurchaseRequest::query()->create([
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'requested_by' => $inventoryUser->getKey(),
        'status' => 'submitted',
        'needed_at' => now()->addDays(2)->toDateString(),
        'notes' => 'Restock rice',
    ]);

    RestaurantPurchaseRequestLine::query()->create([
        'purchase_request_id' => $purchaseRequest->getKey(),
        'item_id' => $restaurantItem->getKey(),
        'uom_id' => $restaurantUom->getKey(),
        'quantity' => 20,
        'notes' => 'Initial request',
    ]);

    $purchaseOrder = RestaurantPurchaseOrder::query()->create([
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'supplier_id' => $restaurantSupplier->getKey(),
        'purchase_request_id' => $purchaseRequest->getKey(),
        'order_no' => 'PO-'.$tenant->slug.'-'.$runId,
        'order_date' => now()->toDateString(),
        'expected_at' => now()->addDays(3)->toDateString(),
        'status' => 'approved',
        'subtotal' => 0,
        'tax_total' => 0,
        'discount_total' => 0,
        'total' => 0,
        'notes' => 'Auto order',
    ]);

    RestaurantPurchaseOrderLine::query()->create([
        'purchase_order_id' => $purchaseOrder->getKey(),
        'item_id' => $restaurantItem->getKey(),
        'uom_id' => $restaurantUom->getKey(),
        'quantity' => 20,
        'unit_cost' => 60000,
        'tax_rate' => 0,
        'tax_amount' => 0,
        'line_total' => 20 * 60000,
    ]);

    $goodsReceipt = RestaurantGoodsReceipt::query()->create([
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'warehouse_id' => $restaurantWarehouses[0]->getKey(),
        'supplier_id' => $restaurantSupplier->getKey(),
        'purchase_order_id' => $purchaseOrder->getKey(),
        'receipt_no' => 'GR-'.$tenant->slug.'-'.$runId,
        'receipt_date' => now()->toDateString(),
        'status' => 'draft',
        'subtotal' => 20 * 60000,
        'tax_total' => 0,
        'total' => 20 * 60000,
        'notes' => 'Goods receipt',
    ]);

    RestaurantGoodsReceiptLine::query()->create([
        'goods_receipt_id' => $goodsReceipt->getKey(),
        'item_id' => $restaurantItem->getKey(),
        'uom_id' => $restaurantUom->getKey(),
        'quantity' => 20,
        'unit_cost' => 60000,
        'batch_no' => 'BATCH-'.$tenant->slug,
        'expires_at' => now()->addMonths(6)->toDateString(),
    ]);

    $goodsReceiptDoc = app(RestaurantGoodsReceiptService::class)->post($goodsReceipt->refresh());
    assertTrue($goodsReceiptDoc->status === 'posted', 'Goods receipt inventory doc not posted');

    $recipe = RestaurantRecipe::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'name' => 'Rice Recipe',
    ], [
        'tenant_id' => $tenant->getKey(),
        'code' => 'REC-'.$tenant->slug,
        'yield_quantity' => 1,
        'yield_uom_id' => $restaurantUom->getKey(),
        'waste_percent' => 0,
        'is_active' => true,
    ]);

    RestaurantRecipeLine::query()->firstOrCreate([
        'recipe_id' => $recipe->getKey(),
        'item_id' => $restaurantItem->getKey(),
        'uom_id' => $restaurantUom->getKey(),
    ], [
        'quantity' => 0.5,
        'waste_percent' => 0,
        'is_optional' => false,
    ]);

    $menuItem = RestaurantMenuItem::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'name' => 'Rice Bowl',
    ], [
        'tenant_id' => $tenant->getKey(),
        'recipe_id' => $recipe->getKey(),
        'code' => 'MENU-'.$tenant->slug,
        'category' => 'main',
        'price' => 150000,
        'is_active' => true,
    ]);

    $menuSale = RestaurantMenuSale::query()->create([
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'warehouse_id' => $restaurantWarehouses[0]->getKey(),
        'sale_date' => now()->toDateString(),
        'source' => 'pos',
        'external_ref' => 'POS-'.$tenant->slug.'-'.$runId,
        'total_amount' => 0,
        'status' => 'draft',
    ]);

    RestaurantMenuSaleLine::query()->create([
        'menu_sale_id' => $menuSale->getKey(),
        'menu_item_id' => $menuItem->getKey(),
        'quantity' => 2,
        'unit_price' => 150000,
        'line_total' => 300000,
    ]);

    $menuSaleDoc = app(RestaurantMenuSaleService::class)->post($menuSale->refresh());
    assertTrue($menuSaleDoc->status === 'posted', 'Menu sale consumption doc not posted');

    $fund = PettyCashFund::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'name' => 'Main Fund',
    ], [
        'tenant_id' => $tenant->getKey(),
        'code' => 'FUND-'.$tenant->slug,
        'status' => PettyCashStatuses::FUND_ACTIVE,
        'currency' => 'IRR',
        'opening_balance' => 1000000,
        'current_balance' => 1000000,
        'threshold_balance' => 200000,
        'replenishment_amount' => 500000,
        'accounting_cash_account_id' => $accCash->getKey(),
        'accounting_source_account_id' => $accBank->getKey(),
        'default_expense_account_id' => $accPettyExpense->getKey(),
        'accounting_treasury_account_id' => $treasury->getKey(),
    ]);

    $category = PettyCashCategory::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'name' => 'Office Supplies',
    ], [
        'tenant_id' => $tenant->getKey(),
        'accounting_account_id' => $accPettyExpense->getKey(),
        'code' => 'CAT-'.$tenant->slug,
        'status' => 'active',
    ]);

    $expense = PettyCashExpense::query()->create([
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'fund_id' => $fund->getKey(),
        'category_id' => $category->getKey(),
        'expense_date' => now()->toDateString(),
        'amount' => 120000,
        'currency' => 'IRR',
        'status' => PettyCashStatuses::EXPENSE_DRAFT,
        'reference' => 'EXP-'.$tenant->slug.'-'.$runId,
        'payee_name' => 'Vendor',
        'description' => 'Petty cash expense',
        'receipt_required' => true,
        'has_receipt' => false,
    ]);

    $receiptDir = storage_path('app/public/petty-cash/expenses');
    if (! is_dir($receiptDir)) {
        mkdir($receiptDir, 0775, true);
    }
    $receiptFile = $receiptDir.'/receipt-'.$tenant->slug.'.txt';
    file_put_contents($receiptFile, 'scenario receipt');

    PettyCashExpenseAttachment::query()->create([
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'expense_id' => $expense->getKey(),
        'uploaded_by' => $superAdmin->getKey(),
        'path' => 'petty-cash/expenses/receipt-'.$tenant->slug.'.txt',
        'original_name' => 'receipt-'.$tenant->slug.'.txt',
        'mime_type' => 'text/plain',
        'size' => filesize($receiptFile),
    ]);

    $pettyService = app(PettyCashPostingService::class);
    $expense = $pettyService->submitExpense($expense, $superAdmin->getKey());
    $expense = $pettyService->approveExpense($expense, $superAdmin->getKey());
    $expense = $pettyService->postExpense($expense, $superAdmin->getKey());
    assertTrue($expense->status === PettyCashStatuses::EXPENSE_PAID, 'Petty cash expense not paid');

    $replenishment = PettyCashReplenishment::query()->create([
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'fund_id' => $fund->getKey(),
        'request_date' => now()->toDateString(),
        'amount' => 300000,
        'currency' => 'IRR',
        'status' => PettyCashStatuses::REPLENISHMENT_DRAFT,
        'source_treasury_account_id' => $treasury->getKey(),
        'description' => 'Top up fund',
    ]);

    $replenishment = $pettyService->submitReplenishment($replenishment, $superAdmin->getKey());
    $replenishment = $pettyService->approveReplenishment($replenishment, $superAdmin->getKey());
    $replenishment = $pettyService->postReplenishment($replenishment, $superAdmin->getKey());
    assertTrue($replenishment->status === PettyCashStatuses::REPLENISHMENT_PAID, 'Replenishment not paid');

    $settlement = PettyCashSettlement::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'fund_id' => $fund->getKey(),
        'period_start' => now()->startOfMonth()->toDateString(),
        'period_end' => now()->endOfMonth()->toDateString(),
    ], [
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'status' => PettyCashStatuses::SETTLEMENT_DRAFT,
        'notes' => 'Monthly settlement',
    ]);

    PettyCashSettlementItem::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'settlement_id' => $settlement->getKey(),
        'expense_id' => $expense->getKey(),
    ]);

    if ($settlement->status !== PettyCashStatuses::SETTLEMENT_POSTED) {
        $settlement = $pettyService->submitSettlement($settlement, $superAdmin->getKey());
        $settlement = $pettyService->approveSettlement($settlement, $superAdmin->getKey());
        $settlement = $pettyService->postSettlement($settlement, $superAdmin->getKey());
    }
    assertTrue($settlement->status === PettyCashStatuses::SETTLEMENT_POSTED, 'Settlement not posted');

    PayrollMinimumWageTable::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'effective_from' => now()->startOfYear()->toDateString(),
    ], [
        'tenant_id' => $tenant->getKey(),
        'daily_wage' => 3463656,
        'monthly_wage' => 103909680,
        'description' => 'Scenario wage table',
    ]);

    PayrollAllowanceTable::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'effective_from' => now()->startOfYear()->toDateString(),
    ], [
        'tenant_id' => $tenant->getKey(),
        'housing_allowance' => 9000000,
        'food_allowance' => 22000000,
        'child_allowance_daily' => 3463656,
        'marriage_allowance' => 5000000,
        'seniority_allowance_daily' => 282000,
        'description' => 'Scenario allowance table',
    ]);

    PayrollInsuranceTable::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'effective_from' => now()->startOfYear()->toDateString(),
    ], [
        'tenant_id' => $tenant->getKey(),
        'employee_rate' => 7,
        'employer_rate' => 23,
        'max_insurable_daily' => 7 * 3463656,
        'max_insurable_monthly' => 7 * 3463656 * 30,
        'description' => 'Scenario insurance table',
    ]);

    $taxTable = PayrollTaxTable::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'effective_from' => now()->startOfYear()->toDateString(),
    ], [
        'tenant_id' => $tenant->getKey(),
        'exemption_amount' => 240000000,
        'flat_allowance_rate' => 10,
        'description' => 'Scenario tax table',
    ]);

    PayrollTaxBracket::query()->firstOrCreate([
        'payroll_tax_table_id' => $taxTable->getKey(),
        'min_amount' => 240000000,
        'max_amount' => 300000000,
    ], [
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'rate' => 10,
    ]);

    PayrollTaxBracket::query()->firstOrCreate([
        'payroll_tax_table_id' => $taxTable->getKey(),
        'min_amount' => 300000001,
        'max_amount' => 380000000,
    ], [
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'rate' => 15,
    ]);

    PayrollTaxBracket::query()->firstOrCreate([
        'payroll_tax_table_id' => $taxTable->getKey(),
        'min_amount' => 380000001,
        'max_amount' => 500000000,
    ], [
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'rate' => 20,
    ]);

    $shift = PayrollAttendanceShift::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'code' => 'SHIFT-DAY',
    ], [
        'tenant_id' => $tenant->getKey(),
        'name' => 'Day Shift',
        'start_time' => '08:00:00',
        'end_time' => '16:00:00',
        'break_minutes' => 30,
        'is_night' => false,
        'is_rotating' => false,
        'is_active' => true,
    ]);

    $employee = PayrollEmployee::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'employee_no' => 'EMP-'.$tenant->slug,
    ], [
        'tenant_id' => $tenant->getKey(),
        'first_name' => 'Ali',
        'last_name' => 'Scenario',
        'national_id' => '0012345678',
        'marital_status' => 'married',
        'children_count' => 1,
        'employment_date' => now()->subYears(2)->toDateString(),
        'job_title' => 'Chef',
        'status' => 'active',
        'bank_name' => 'Sample Bank',
        'bank_account' => '1234567890',
        'bank_sheba' => 'IR000000000000000000000000',
    ]);

    PayrollContract::query()->updateOrCreate([
        'employee_id' => $employee->getKey(),
        'company_id' => $company->getKey(),
        'scope' => 'official',
    ], [
        'tenant_id' => $tenant->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'status' => 'active',
        'effective_from' => now()->startOfYear()->toDateString(),
        'base_salary' => 150000000,
        'monthly_hours' => 176,
        'overtime_allowed' => true,
        'night_shift_allowed' => false,
        'insurance_included' => true,
        'tax_included' => true,
    ]);

    PayrollContract::query()->updateOrCreate([
        'employee_id' => $employee->getKey(),
        'company_id' => $company->getKey(),
        'scope' => 'internal',
    ], [
        'tenant_id' => $tenant->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'status' => 'active',
        'effective_from' => now()->startOfYear()->toDateString(),
        'base_salary' => 250000000,
        'monthly_hours' => 176,
        'overtime_allowed' => true,
        'night_shift_allowed' => true,
        'insurance_included' => false,
        'tax_included' => false,
    ]);

    $periodStart = Carbon::now()->startOfMonth();
    for ($d = 0; $d < 5; $d++) {
        $date = $periodStart->copy()->addDays($d);
        PayrollAttendanceRecord::query()->firstOrCreate([
            'employee_id' => $employee->getKey(),
            'work_date' => $date->toDateString(),
        ], [
            'tenant_id' => $tenant->getKey(),
            'company_id' => $company->getKey(),
            'branch_id' => $branches[0]->getKey(),
            'shift_id' => $shift->getKey(),
            'scheduled_in' => $date->copy()->setTime(8, 0),
            'scheduled_out' => $date->copy()->setTime(16, 0),
            'actual_in' => $date->copy()->setTime(8, 5),
            'actual_out' => $date->copy()->setTime(16, 30),
            'worked_minutes' => 480,
            'late_minutes' => 5,
            'early_leave_minutes' => 0,
            'overtime_minutes' => 30,
            'night_minutes' => 0,
            'friday_minutes' => 0,
            'holiday_minutes' => 0,
            'absence_minutes' => 0,
            'status' => 'approved',
            'approved_by' => $superAdmin->getKey(),
            'approved_at' => now(),
        ]);
    }

    $loan = PayrollLoan::query()->firstOrCreate([
        'employee_id' => $employee->getKey(),
        'company_id' => $company->getKey(),
        'status' => 'active',
    ], [
        'tenant_id' => $tenant->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'amount' => 30000000,
        'installment_count' => 3,
        'installment_amount' => 10000000,
        'start_date' => now()->startOfMonth()->toDateString(),
        'notes' => 'Scenario loan',
    ]);

    PayrollLoanInstallment::query()->firstOrCreate([
        'loan_id' => $loan->getKey(),
        'due_date' => now()->endOfMonth()->toDateString(),
    ], [
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'amount' => 10000000,
        'status' => 'due',
    ]);

    PayrollAdvance::query()->firstOrCreate([
        'employee_id' => $employee->getKey(),
        'company_id' => $company->getKey(),
        'advance_date' => now()->startOfMonth()->toDateString(),
    ], [
        'tenant_id' => $tenant->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'amount' => 5000000,
        'status' => 'open',
        'notes' => 'Scenario advance',
    ]);

    $payrollRun = PayrollRun::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'period_start' => $periodStart->toDateString(),
        'period_end' => Carbon::now()->endOfMonth()->toDateString(),
    ], [
        'status' => 'draft',
        'notes' => 'Scenario payroll run',
    ]);

    app(PayrollRunService::class)->generate($payrollRun->refresh());
    assertTrue($payrollRun->slips()->count() >= 2, 'Payroll slips missing');

    $workflow = Workflow::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'name' => 'Scenario Workflow',
    ], [
        'description' => 'Scenario workflow',
        'is_default' => true,
        'created_by' => $superAdmin->getKey(),
        'updated_by' => $superAdmin->getKey(),
    ]);

    $statusTodo = Status::query()->firstOrCreate([
        'workflow_id' => $workflow->getKey(),
        'slug' => 'todo',
    ], [
        'tenant_id' => $tenant->getKey(),
        'name' => 'Todo',
        'category' => 'todo',
        'color' => '#f59e0b',
        'sort_order' => 1,
        'is_default' => true,
    ]);

    $statusInProgress = Status::query()->firstOrCreate([
        'workflow_id' => $workflow->getKey(),
        'slug' => 'in-progress',
    ], [
        'tenant_id' => $tenant->getKey(),
        'name' => 'In Progress',
        'category' => 'in_progress',
        'color' => '#3b82f6',
        'sort_order' => 2,
        'is_default' => false,
    ]);

    Transition::query()->firstOrCreate([
        'workflow_id' => $workflow->getKey(),
        'from_status_id' => $statusTodo->getKey(),
        'to_status_id' => $statusInProgress->getKey(),
    ], [
        'tenant_id' => $tenant->getKey(),
        'name' => 'Start',
        'is_active' => true,
        'sort_order' => 1,
        'validators' => [],
    ]);

    $workType = WorkType::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'slug' => 'task',
    ], [
        'name' => 'Task',
        'description' => 'Scenario task',
        'icon' => 'heroicon-o-clipboard',
        'color' => '#10b981',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $project = Project::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'key' => strtoupper(substr($tenant->slug, 0, 3)).'-PRJ',
    ], [
        'workflow_id' => $workflow->getKey(),
        'name' => 'Scenario Project '.$tenant->slug,
        'status' => 'active',
        'lead_user_id' => $superAdmin->getKey(),
        'created_by' => $superAdmin->getKey(),
        'updated_by' => $superAdmin->getKey(),
    ]);

    $workItem = app(WorkItemCreator::class)->create([
        'tenant_id' => $tenant->getKey(),
        'project_id' => $project->getKey(),
        'work_type_id' => $workType->getKey(),
        'title' => 'Scenario Work Item',
        'description' => 'Workhub scenario item',
        'priority' => 'medium',
        'assignee_id' => $manager->getKey(),
        'reporter_id' => $superAdmin->getKey(),
    ]);

    $workItem = app(WorkflowTransitionService::class)->transition($workItem, $statusInProgress);
    assertTrue($workItem->status_id === $statusInProgress->getKey(), 'Work item transition failed');

    logLine('tenant scenarios ok: '.$tenant->slug);
}

TenantContext::setTenant(null);
app(PermissionRegistrar::class)->setPermissionsTeamId(null);

logLine('deep scenario runner completed');
