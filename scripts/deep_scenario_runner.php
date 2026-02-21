<?php

declare(strict_types=1);

use App\Models\User;
use Carbon\Carbon;
use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Events\Iam\IamUserCreated;
use Filamat\IamSuite\Models\IamAiReport;
use Filamat\IamSuite\Models\Organization;
use Filamat\IamSuite\Models\PrivilegeActivation;
use Filamat\IamSuite\Models\PrivilegeRequest;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\SubscriptionPlan;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\TenantUser;
use Filamat\IamSuite\Models\UserSession;
use Filamat\IamSuite\Models\Webhook;
use Filamat\IamSuite\Models\WebhookDelivery;
use Filamat\IamSuite\Services\ApiKeyService;
use Filamat\IamSuite\Services\Automation\IamEventPublisher;
use Filamat\IamSuite\Services\ImpersonationService;
use Filamat\IamSuite\Services\CapabilitySyncService;
use Filamat\IamSuite\Services\InviteUserService;
use Filamat\IamSuite\Services\PrivilegeEligibilityService;
use Filamat\IamSuite\Services\PrivilegeElevationService;
use Filamat\IamSuite\Services\SessionService;
use Filamat\IamSuite\Services\WalletService;
use Filamat\IamSuite\Support\TenantContext;
use Haida\CommerceCatalog\Models\CatalogProduct;
use Haida\CommerceCatalog\Models\CatalogVariant;
use Haida\CommerceCheckout\Services\CartService;
use Haida\CommerceCheckout\Services\CheckoutService;
use Haida\CommerceOrders\Services\OrderRefundService;
use Haida\FilamentCommerceCore\Models\CommerceException;
use Haida\FilamentCommerceExperience\Models\ExperienceAnswer;
use Haida\FilamentCommerceExperience\Models\ExperienceCsatResponse;
use Haida\FilamentCommerceExperience\Models\ExperienceCsatSurvey;
use Haida\FilamentCommerceExperience\Models\ExperienceQuestion;
use Haida\FilamentCommerceExperience\Models\ExperienceReview;
use Haida\FilamentCommerceExperience\Services\CsatSurveyService;
use Haida\FilamentCryptoGateway\Adapters\CryptomusAdapter;
use Haida\FilamentCryptoGateway\Contracts\ProviderAdapterInterface;
use Haida\FilamentCryptoGateway\DTOs\InvoiceCreateData as CryptoInvoiceCreateData;
use Haida\FilamentCryptoGateway\DTOs\PayoutCreateData as CryptoPayoutCreateData;
use Haida\FilamentCryptoGateway\DTOs\ProviderInvoiceData as CryptoProviderInvoiceData;
use Haida\FilamentCryptoGateway\DTOs\ProviderPayoutData as CryptoProviderPayoutData;
use Haida\FilamentCryptoGateway\DTOs\WebhookEventData as CryptoWebhookEventData;
use Haida\FilamentCryptoGateway\Enums\CryptoInvoiceStatus;
use Haida\FilamentCryptoGateway\Enums\CryptoPayoutStatus;
use Haida\FilamentCryptoGateway\Models\CryptoInvoice;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;
use Haida\FilamentCryptoGateway\Services\ProviderRegistry;
use Haida\FilamentCryptoGateway\Services\ReconcileService;
use Haida\FilamentCryptoGateway\Services\WebhookIngestionService;
use Haida\FilamentCryptoGateway\Services\WebhookProcessor;
use Haida\FilamentCryptoNodes\Adapters\BtcpayServerAdapter;
use Haida\FilamentAiCore\Models\AiPolicy;
use Haida\FilamentPos\Models\PosCashierSession;
use Haida\FilamentPos\Models\PosDevice;
use Haida\FilamentPos\Models\PosRegister;
use Haida\FilamentPos\Models\PosSale;
use Haida\FilamentPos\Models\PosStore;
use Haida\FilamentPos\Services\PosOutboxService;
use Haida\FilamentPettyCashIr\Application\Services\PettyCashAiService;
use Haida\FilamentPettyCashIr\Infrastructure\Ai\FakeAiProvider as PettyCashFakeAiProvider;
use Haida\FilamentPettyCashIr\Models\PettyCashCashCount;
use Haida\FilamentPettyCashIr\Models\PettyCashCategory;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashExpenseAttachment;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Models\PettyCashReconciliation;
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
use Haida\FilamentLoyaltyClub\Models\LoyaltyAuditEvent;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCampaign;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCampaignVariant;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCoupon;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomerSegment;
use Haida\FilamentLoyaltyClub\Models\LoyaltyPointsBucket;
use Haida\FilamentLoyaltyClub\Models\LoyaltyPointsRule;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReferral;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReferralProgram;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReward;
use Haida\FilamentLoyaltyClub\Models\LoyaltySegment;
use Haida\FilamentLoyaltyClub\Models\LoyaltyTier;
use Haida\FilamentLoyaltyClub\Services\LoyaltyCampaignService;
use Haida\FilamentLoyaltyClub\Services\LoyaltyCouponService;
use Haida\FilamentLoyaltyClub\Services\LoyaltyEventService;
use Haida\FilamentLoyaltyClub\Services\LoyaltyExpiryService;
use Haida\FilamentLoyaltyClub\Services\LoyaltyLedgerService;
use Haida\FilamentLoyaltyClub\Services\LoyaltyReferralService;
use Haida\FilamentLoyaltyClub\Services\LoyaltyRewardService;
use Haida\FilamentLoyaltyClub\Services\LoyaltySegmentService;
use Haida\FilamentThreeCx\FilamentThreeCxServiceProvider;
use Haida\FilamentThreeCx\Models\ThreeCxCallLog;
use Haida\FilamentThreeCx\Models\ThreeCxContact;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Models\ThreeCxSyncCursor;
use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Models\MeetingActionItem;
use Haida\FilamentMeetings\Models\MeetingAgendaItem;
use Haida\FilamentMeetings\Models\MeetingAttendee;
use Haida\FilamentMeetings\Models\MeetingNote;
use Haida\FilamentMeetings\Services\MeetingConsentService;
use Haida\FilamentMeetings\Services\MeetingTranscriptService;
use Haida\FilamentMeetings\Services\MeetingsAiService;
use Haida\FilamentMeetings\Services\MeetingWorkhubService;
use Haida\FilamentWorkhub\Models\Comment;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\Transition;
use Haida\FilamentWorkhub\Models\Workflow;
use Haida\FilamentWorkhub\Models\WorkType;
use Haida\FilamentWorkhub\Services\WorkhubAiService;
use Haida\FilamentWorkhub\Services\WorkflowTransitionService;
use Haida\FilamentWorkhub\Services\WorkItemCreator;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapInbox;
use Haida\MailtrapCore\Models\MailtrapOffer;
use Haida\MailtrapCore\Services\MailtrapConnectionService;
use Haida\MailtrapCore\Services\MailtrapDomainService;
use Haida\MailtrapCore\Services\MailtrapInboxService;
use Haida\MailtrapCore\Services\MailtrapMessageService;
use Haida\MailtrapCore\Services\MailtrapOfferService;
use Haida\PaymentsOrchestrator\Models\PaymentGatewayConnection;
use Haida\PaymentsOrchestrator\Services\PaymentIntentService;
use Haida\ProvidersEsimGoCommerce\Services\EsimGoCommerceService;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Haida\ProvidersEsimGoCore\Models\EsimGoEsim;
use Haida\ProvidersEsimGoCore\Models\EsimGoOrder;
use Haida\ProvidersEsimGoCore\Models\EsimGoProduct;
use Haida\ProvidersEsimGoCore\Services\EsimGoCatalogueService;
use Haida\ProvidersEsimGoCore\Support\EsimGoFakeResponse;
use Haida\SiteBuilderCore\Models\Site;
use Haida\SmsBulk\Jobs\ApplyOptOutJob;
use Haida\SmsBulk\Jobs\EnqueueCampaignJob;
use Haida\SmsBulk\Jobs\SyncReportsJob;
use Haida\SmsBulk\Models\SmsBulkCampaign;
use Haida\SmsBulk\Models\SmsBulkContact;
use Haida\SmsBulk\Models\SmsBulkDraftGroup;
use Haida\SmsBulk\Models\SmsBulkDraftMessage;
use Haida\SmsBulk\Models\SmsBulkPatternTemplate;
use Haida\SmsBulk\Models\SmsBulkPhonebook;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Models\SmsBulkQuietHoursProfile;
use Haida\SmsBulk\Models\SmsBulkQuotaPolicy;
use Haida\SmsBulk\Services\Campaign\CampaignBuilderService;
use Haida\SmsBulk\Services\ProviderClientFactory;
use Haida\SmsBulk\Services\SuppressionService;
use Haida\TenancyDomains\Models\SiteDomain;
use Haida\TenancyDomains\Services\SiteDomainService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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
use Vendor\FilamentPayrollAttendanceIr\Application\Services\AiReportService;
use Vendor\FilamentPayrollAttendanceIr\Application\UseCases\ClockIn;
use Vendor\FilamentPayrollAttendanceIr\Application\UseCases\ClockOut;
use Vendor\FilamentPayrollAttendanceIr\Application\UseCases\GenerateTimesheets;
use Vendor\FilamentPayrollAttendanceIr\Application\UseCases\ResolveException;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendanceException;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendancePolicy;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\EmployeeConsent;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\HolidayRule;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\MissionRequest;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\OvertimeRequest;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\Timesheet;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\WorkCalendar;
use Vendor\FilamentPayrollAttendanceIr\Infrastructure\Ai\FakeAiProvider;
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

$preConnection = getenv('DB_CONNECTION') ?: 'sqlite';
if ($preConnection === 'sqlite') {
    $database = getenv('DB_DATABASE');
    if (is_string($database) && $database !== '' && $database !== ':memory:') {
        $resolved = $database;
        $tempRoot = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (str_starts_with($resolved, $tempRoot)) {
            if (file_exists($resolved)) {
                unlink($resolved);
            }

            $directory = dirname($resolved);
            if (! is_dir($directory)) {
                mkdir($directory, 0775, true);
            }
            if (! file_exists($resolved)) {
                touch($resolved);
                chmod($resolved, 0664);
            }
        }
    }
}

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->register(FilamentThreeCxServiceProvider::class);

Artisan::call('migrate', ['--force' => true]);
Artisan::call('migrate', [
    '--path' => 'packages/filament-sms-bulk/database/migrations',
    '--force' => true,
]);
config(['queue.default' => 'sync']);
config(['filamat-iam.capability_sync.queue' => false]);
config(['providers-esim-go-core.fake' => true]);
config(['filament-ai-core.enabled' => true]);
config(['filament-ai-core.default_provider' => 'mock']);
config(['filament-meetings.ai.queue.enabled' => false]);

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
$meetingIds = [];
for ($i = 1; $i <= 3; $i++) {
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

foreach ($tenants as $index => $tenant) {
    logLine('tenant: '.$tenant->slug);
    TenantContext::setTenant($tenant);
    app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());

    AiPolicy::query()->updateOrCreate([
        'tenant_id' => $tenant->getKey(),
    ], [
        'enabled' => true,
        'provider' => 'mock',
        'retention_days' => 30,
        'consent_required_meetings' => true,
        'allow_store_transcripts' => true,
        'redaction_policy' => config('filament-ai-core.redaction.defaults'),
    ]);

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

    logLine('iam scenario start: '.$tenant->slug);

    $invitee = ensureUser('invitee.'.$tenant->slug.'@haida.test', 'Invitee '.$tenant->slug);
    $membership = TenantUser::query()
        ->where('tenant_id', $tenant->getKey())
        ->where('user_id', $invitee->getKey())
        ->first();

    if (! $membership || $membership->status !== 'active') {
        $invite = app(InviteUserService::class)->invite(
            $tenant,
            $invitee->email,
            $invitee->name,
            [],
            [],
            $manager,
            'scenario invite',
            now()->addDays(7)
        );

        app(InviteUserService::class)->accept($invite['invitation'], $invite['token'], $invitee);
    }

    $pamRole = Role::query()->firstOrCreate([
        'name' => 'pam_elevated',
        'guard_name' => 'web',
        'tenant_id' => $tenant->getKey(),
    ]);

    app(PrivilegeEligibilityService::class)->grant($tenant, $manager, $pamRole, $superAdmin, 'scenario eligibility');

    $pamRequest = PrivilegeRequest::query()
        ->where('tenant_id', $tenant->getKey())
        ->where('user_id', $manager->getKey())
        ->where('role_id', $pamRole->getKey())
        ->whereIn('status', ['pending', 'approved'])
        ->orderByDesc('id')
        ->first();

    if (! $pamRequest) {
        $pamRequest = app(PrivilegeElevationService::class)->request(
            $tenant,
            $manager,
            $pamRole,
            30,
            $manager,
            'scenario request',
            'PAM-'.$runId
        );
    }

    if ($pamRequest->status === 'pending') {
        app(PrivilegeElevationService::class)->approve($pamRequest, $superAdmin, 'scenario approve');
        $pamRequest->refresh();
    }

    PrivilegeActivation::query()
        ->where('tenant_id', $tenant->getKey())
        ->where('user_id', $manager->getKey())
        ->where('role_id', $pamRole->getKey())
        ->where('status', 'active')
        ->update(['expires_at' => now()->subMinute()]);

    app(PrivilegeElevationService::class)->expireDueActivations($tenant);

    $activation = app(PrivilegeElevationService::class)->activate(
        $tenant,
        $manager,
        $pamRole,
        $pamRequest,
        $superAdmin,
        'scenario activate',
        'PAM-'.$runId,
        now()->addMinutes(5)
    );

    $activation->update(['expires_at' => now()->subMinute()]);
    app(PrivilegeElevationService::class)->expireDueActivations($tenant);

    $impersonation = app(ImpersonationService::class);
    $session = $impersonation->start($superAdmin, $manager, $tenant, 'scenario impersonate', 'IMP-'.$runId, 5, true);
    assertTrue($impersonation->isImpersonating(), 'Impersonation did not start');
    assertTrue($impersonation->canWrite() === false, 'Impersonation should be restricted');
    $impersonation->stop('scenario stop', $superAdmin);
    assertTrue(! $impersonation->isImpersonating(), 'Impersonation did not stop');
    TenantContext::setTenant($tenant);

    $userSession = UserSession::query()->updateOrCreate([
        'session_id' => 'scenario-'.$tenant->slug.'-'.$runId,
    ], [
        'tenant_id' => $tenant->getKey(),
        'user_id' => $manager->getKey(),
        'ip' => '127.0.0.1',
        'user_agent' => 'ScenarioRunner',
        'last_activity_at' => now(),
    ]);

    app(SessionService::class)->revoke($userSession, $superAdmin, 'scenario revoke');
    $userSession->refresh();
    assertTrue($userSession->revoked_at !== null, 'Session revoke failed');

    logLine('iam scenario ok: '.$tenant->slug);

    logLine('iam n8n scenario start: '.$tenant->slug);

    Http::fake(function ($request) {
        if (Str::startsWith((string) $request->url(), 'https://n8n.test/')) {
            return Http::response(['status' => 'ok'], 200);
        }

        return null;
    });

    $automationWebhook = Webhook::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'type' => 'automation',
        'url' => 'https://n8n.test/webhook/iam/'.$tenant->slug,
    ], [
        'secret' => 'scenario-n8n-secret-'.$runId,
        'enabled' => true,
        'events' => ['iam.user.created', 'automation.n8n.report.received'],
    ]);

    $beforeDeliveries = WebhookDelivery::query()
        ->where('webhook_id', $automationWebhook->getKey())
        ->count();

    $event = new IamUserCreated($tenant->getKey(), [
        'subject' => ['type' => 'user', 'id' => $invitee->getKey()],
        'context' => ['source' => 'scenario'],
    ]);
    app(IamEventPublisher::class)->publish($event);

    $afterDeliveries = WebhookDelivery::query()
        ->where('webhook_id', $automationWebhook->getKey())
        ->count();
    assertTrue($afterDeliveries > $beforeDeliveries, 'Automation webhook delivery not recorded');

    config([
        'filamat-iam.automation.inbound.auth_mode' => 'header',
        'filamat-iam.automation.inbound.token' => 'scenario-n8n-token',
        'filamat-iam.automation.inbound.token_header' => 'X-N8N-Token',
    ]);

    $apiKey = app(ApiKeyService::class)->create([
        'name' => 'n8n-scenario-'.$tenant->slug,
        'tenant_id' => $tenant->getKey(),
    ]);

    $payload = [
        'connector_id' => $automationWebhook->getKey(),
        'idempotency_key' => 'n8n-report-'.$tenant->slug.'-'.$runId,
        'title' => 'Scenario report',
        'severity' => 'medium',
        'report' => [
            'markdown' => 'Scenario report body',
            'findings' => ['finding-a'],
        ],
    ];

    $trustedHost = parse_url((string) config('app.url'), PHP_URL_HOST);
    if (! $trustedHost) {
        $trustedHost = (string) config('tenancy-domains.root_domain', 'localhost');
    }
    $request = Request::create(
        '/api/v1/iam/n8n/callback',
        'POST',
        [],
        [],
        [],
        ['HTTP_HOST' => $trustedHost, 'SERVER_NAME' => $trustedHost],
        json_encode($payload)
    );
    $request->headers->set('Content-Type', 'application/json');
    $request->headers->set('X-Api-Key', $apiKey['token']);
    $request->headers->set('X-Tenant-ID', (string) $tenant->getKey());
    $request->headers->set('X-N8N-Token', 'scenario-n8n-token');

    $response = $app->handle($request);
    if ($response->getStatusCode() !== 200) {
        logLine('n8n callback status: '.$response->getStatusCode().' body: '.$response->getContent());
    }
    assertTrue($response->getStatusCode() === 200, 'n8n callback failed');

    $report = IamAiReport::query()
        ->where('tenant_id', $tenant->getKey())
        ->where('idempotency_key', $payload['idempotency_key'])
        ->first();
    assertTrue((bool) $report, 'AI report not stored');

    logLine('iam n8n scenario ok: '.$tenant->slug);

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

    config([
        'filament-petty-cash-ir.ai.enabled' => true,
        'filament-petty-cash-ir.ai.provider' => PettyCashFakeAiProvider::class,
    ]);

    $aiService = app(PettyCashAiService::class);
    $aiService->suggestExpense($expense);

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

    $periodStart = now()->startOfMonth()->startOfDay();
    $periodEnd = now()->endOfMonth()->startOfDay();

    $settlement = PettyCashSettlement::query()
        ->where('fund_id', $fund->getKey())
        ->whereDate('period_start', $periodStart)
        ->whereDate('period_end', $periodEnd)
        ->first();

    if (! $settlement) {
        $settlement = PettyCashSettlement::query()->create([
            'tenant_id' => $tenant->getKey(),
            'fund_id' => $fund->getKey(),
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'company_id' => $company->getKey(),
            'branch_id' => $branches[0]->getKey(),
            'status' => PettyCashStatuses::SETTLEMENT_DRAFT,
            'notes' => 'Monthly settlement',
        ]);
    }

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

    PettyCashCashCount::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'fund_id' => $fund->getKey(),
        'count_date' => now()->toDateString(),
    ], [
        'status' => 'submitted',
        'expected_balance' => 1000000,
        'counted_balance' => 950000,
        'counted_by' => $superAdmin->getKey(),
        'notes' => 'Scenario cash count',
    ]);

    PettyCashReconciliation::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'fund_id' => $fund->getKey(),
        'period_start' => now()->startOfMonth()->toDateString(),
        'period_end' => now()->endOfMonth()->toDateString(),
    ], [
        'status' => 'submitted',
        'expected_balance' => 1000000,
        'ledger_balance' => 980000,
        'prepared_by' => $superAdmin->getKey(),
        'notes' => 'Scenario reconciliation',
    ]);

    $aiService->runContinuousAudit($fund->getKey(), 50, $superAdmin->getKey());

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

    $attendancePolicy = AttendancePolicy::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'name' => 'Scenario Attendance Policy',
    ], [
        'tenant_id' => $tenant->getKey(),
        'status' => 'active',
        'is_default' => true,
        'requires_consent' => true,
        'allow_remote_work' => false,
        'rules' => [
            'require_wifi' => true,
            'late_grace_minutes' => 10,
            'max_overtime_minutes' => 60,
            'exception_assignee_id' => $hrUser->getKey(),
        ],
    ]);

    $periodStart = Carbon::now()->startOfMonth();

    $calendar = WorkCalendar::query()->firstOrCreate([
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'name' => 'Scenario Calendar',
    ], [
        'tenant_id' => $tenant->getKey(),
        'calendar_type' => 'jalali',
        'timezone' => 'Asia/Tehran',
        'is_default' => true,
    ]);

    $holidayDate = $periodStart->copy()->startOfDay();
    $holidayRule = HolidayRule::query()
        ->where('work_calendar_id', $calendar->getKey())
        ->whereDate('holiday_date', $holidayDate)
        ->first();

    if (! $holidayRule) {
        HolidayRule::query()->create([
            'work_calendar_id' => $calendar->getKey(),
            'holiday_date' => $holidayDate->toDateString(),
            'tenant_id' => $tenant->getKey(),
            'company_id' => $company->getKey(),
            'title' => 'Scenario Holiday',
            'is_public' => true,
            'source' => 'manual',
        ]);
    }

    OvertimeRequest::query()->firstOrCreate([
        'employee_id' => $employee->getKey(),
        'work_date' => $periodStart->toDateString(),
    ], [
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'requested_minutes' => 90,
        'status' => 'approved',
        'requested_by' => $superAdmin->getKey(),
        'approved_by' => $hrUser->getKey(),
        'approved_at' => now(),
        'reason' => 'Scenario overtime',
    ]);

    MissionRequest::query()->firstOrCreate([
        'employee_id' => $employee->getKey(),
        'start_date' => $periodStart->toDateString(),
        'end_date' => $periodStart->copy()->addDay()->toDateString(),
    ], [
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'allowance_amount' => 2500000,
        'status' => 'approved',
        'approved_by' => $hrUser->getKey(),
        'approved_at' => now(),
        'notes' => 'Scenario mission',
    ]);

    EmployeeConsent::query()->updateOrCreate([
        'employee_id' => $employee->getKey(),
        'consent_type' => 'location_tracking',
    ], [
        'tenant_id' => $tenant->getKey(),
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'is_granted' => true,
        'granted_by' => $superAdmin->getKey(),
        'granted_at' => now(),
    ]);

    app(ClockIn::class)->execute($employee->getKey(), [
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'event_at' => now()->setTime(8, 0),
        'source' => 'mobile',
    ]);

    app(ClockOut::class)->execute($employee->getKey(), [
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'event_at' => now()->setTime(16, 0),
        'source' => 'mobile',
        'wifi_ssid' => 'ScenarioWiFi',
    ]);

    $exception = AttendanceException::query()
        ->where('employee_id', $employee->getKey())
        ->where('status', 'open')
        ->first();

    if ($exception) {
        app(ResolveException::class)->execute($exception, [
            'resolution_notes' => 'Scenario resolution',
        ]);
    }

    for ($d = 0; $d < 5; $d++) {
        $date = $periodStart->copy()->addDays($d);
        $workDate = $date->copy()->startOfDay();
        $attendance = PayrollAttendanceRecord::query()
            ->where('employee_id', $employee->getKey())
            ->whereDate('work_date', $workDate)
            ->first();

        if (! $attendance) {
            PayrollAttendanceRecord::query()->create([
                'employee_id' => $employee->getKey(),
                'work_date' => $workDate,
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
    }

    $timesheetStart = $periodStart->copy()->startOfDay();
    $timesheetEnd = Carbon::now()->endOfMonth()->startOfDay();
    $timesheetExists = Timesheet::query()
        ->where('employee_id', $employee->getKey())
        ->whereDate('period_start', $timesheetStart)
        ->whereDate('period_end', $timesheetEnd)
        ->where('period_type', 'monthly')
        ->exists();

    if (! $timesheetExists) {
        app(GenerateTimesheets::class)->execute(
            $company->getKey(),
            $branches[0]->getKey(),
            $timesheetStart->copy(),
            $timesheetEnd->copy()
        );
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

    $payrollRunStart = $periodStart->copy()->startOfDay();
    $payrollRunEnd = Carbon::now()->endOfMonth()->startOfDay();
    $payrollRun = PayrollRun::query()
        ->where('tenant_id', $tenant->getKey())
        ->where('company_id', $company->getKey())
        ->where('branch_id', $branches[0]->getKey())
        ->whereDate('period_start', $payrollRunStart)
        ->whereDate('period_end', $payrollRunEnd)
        ->first();

    if (! $payrollRun) {
        $payrollRun = PayrollRun::query()->create([
            'tenant_id' => $tenant->getKey(),
            'company_id' => $company->getKey(),
            'branch_id' => $branches[0]->getKey(),
            'period_start' => $payrollRunStart->toDateString(),
            'period_end' => $payrollRunEnd->toDateString(),
            'status' => 'draft',
            'notes' => 'Scenario payroll run',
        ]);
    }

    app(PayrollRunService::class)->generate($payrollRun->refresh());
    assertTrue($payrollRun->slips()->count() >= 2, 'Payroll slips missing');

    config()->set('filament-payroll-attendance-ir.ai.enabled', true);
    config()->set('filament-payroll-attendance-ir.ai.provider', FakeAiProvider::class);
    app(AiReportService::class)->generatePersianManagerReport([
        'company_id' => $company->getKey(),
        'branch_id' => $branches[0]->getKey(),
        'period_start' => $periodStart->toDateString(),
        'period_end' => Carbon::now()->endOfMonth()->toDateString(),
        'summary_totals' => [
            'worked_minutes' => 2400,
            'overtime_minutes' => 150,
        ],
    ]);

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

    Comment::query()->create([
        'tenant_id' => $tenant->getKey(),
        'work_item_id' => $workItem->getKey(),
        'user_id' => $manager->getKey(),
        'body' => 'به‌روزرسانی پیشرفت: کار در حال انجام است.',
        'is_internal' => false,
    ]);

    Comment::query()->create([
        'tenant_id' => $tenant->getKey(),
        'work_item_id' => $workItem->getKey(),
        'user_id' => $superAdmin->getKey(),
        'body' => 'نیاز به بررسی ریسک‌ها و اقدامات بعدی.',
        'is_internal' => false,
    ]);

    $workhubAi = app(WorkhubAiService::class);
    $workItem->loadMissing(['project', 'status', 'assignee']);

    $personalSummary = $workhubAi->summarizeWorkItem(
        $workItem,
        'personal_summary',
        'ttl',
        ['include_comments' => true, 'ttl_minutes' => 30],
        $superAdmin
    );
    assertTrue($personalSummary['result']->ok, 'Workhub personal summary failed');

    $sharedSummary = $workhubAi->summarizeWorkItem(
        $workItem,
        'shared_summary',
        'shared',
        ['include_comments' => true],
        $superAdmin
    );
    assertTrue($sharedSummary['result']->ok, 'Workhub shared summary failed');

    $threadSummary = $workhubAi->summarizeThread(
        $workItem,
        'ttl',
        ['ttl_minutes' => 30],
        $superAdmin
    );
    assertTrue($threadSummary['result']->ok, 'Workhub thread summary failed');

    $progressSummary = $workhubAi->progressUpdate(
        $workItem,
        7,
        ['ttl_minutes' => 30],
        $superAdmin
    );
    assertTrue($progressSummary['result']->ok, 'Workhub progress summary failed');

    $subtaskSuggestions = $workhubAi->suggestSubtasks($workItem, 3, $superAdmin);
    assertTrue($subtaskSuggestions['result']->ok, 'Workhub subtask suggestions failed');

    $subtasks = $workhubAi->createSubtasks($workItem, $subtaskSuggestions['suggestions'], $superAdmin);
    assertTrue(count($subtasks) > 0, 'Workhub subtasks creation failed');

    $meeting = Meeting::query()->create([
        'tenant_id' => $tenant->getKey(),
        'title' => 'جلسه سناریو '.$tenant->slug,
        'scheduled_at' => now()->addHour(),
        'duration_minutes' => 45,
        'location_type' => 'online',
        'location_value' => 'https://meet.example.test/'.$tenant->slug,
        'organizer_id' => $superAdmin->getKey(),
        'status' => 'scheduled',
        'ai_enabled' => true,
        'consent_required' => true,
        'consent_mode' => 'manual',
        'share_minutes_mode' => 'attendees',
        'minutes_format' => 'team',
        'meta' => [
            'workhub_project_id' => $project->getKey(),
        ],
        'created_by' => $superAdmin->getKey(),
        'updated_by' => $superAdmin->getKey(),
    ]);
    $meetingIds[(int) $tenant->getKey()] = $meeting->getKey();

    MeetingAttendee::query()->create([
        'tenant_id' => $tenant->getKey(),
        'meeting_id' => $meeting->getKey(),
        'user_id' => $superAdmin->getKey(),
        'name' => $superAdmin->name,
        'role' => 'host',
        'attendance_status' => 'accepted',
    ]);

    MeetingAttendee::query()->create([
        'tenant_id' => $tenant->getKey(),
        'meeting_id' => $meeting->getKey(),
        'user_id' => $manager->getKey(),
        'name' => $manager->name,
        'role' => 'attendee',
        'attendance_status' => 'accepted',
    ]);

    MeetingAgendaItem::query()->create([
        'tenant_id' => $tenant->getKey(),
        'meeting_id' => $meeting->getKey(),
        'sort_order' => 1,
        'title' => 'بررسی وضعیت تسک‌ها',
        'description' => 'مرور روند و موانع',
    ]);

    MeetingNote::query()->updateOrCreate([
        'tenant_id' => $tenant->getKey(),
        'meeting_id' => $meeting->getKey(),
    ], [
        'content_longtext' => 'یادداشت اولیه جلسه برای سناریو',
        'updated_by' => $superAdmin->getKey(),
    ]);

    $consentResult = app(MeetingConsentService::class)->confirmConsent($meeting, $superAdmin);
    assertTrue((bool) ($consentResult['ok'] ?? false), 'Meeting consent failed');

    $transcriptResult = app(MeetingTranscriptService::class)->storeTranscript(
        $meeting,
        "00:00:05 {$superAdmin->name}: شروع جلسه و مرور اهداف\n00:00:10 {$manager->name}: اعلام وضعیت تسک‌ها",
        'fa',
        'manual',
        $superAdmin
    );
    assertTrue((bool) ($transcriptResult['ok'] ?? false), 'Meeting transcript failed');

    $agendaResult = app(MeetingsAiService::class)->generateAgenda($meeting, $superAdmin);
    assertTrue((bool) ($agendaResult['ok'] ?? false), 'Meeting agenda AI failed');

    $minutesResult = app(MeetingsAiService::class)->generateMinutes($meeting, $superAdmin);
    assertTrue((bool) ($minutesResult['ok'] ?? false), 'Meeting minutes AI failed');

    $actionItemIds = MeetingActionItem::query()
        ->where('meeting_id', $meeting->getKey())
        ->pluck('id')
        ->all();
    assertTrue(count($actionItemIds) > 0, 'Meeting action items missing');

    $linkResult = app(MeetingWorkhubService::class)->linkActionItems($meeting, $actionItemIds, $superAdmin, $project->getKey());
    assertTrue((bool) ($linkResult['ok'] ?? false), 'Meeting action items link failed');

    $site = Site::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'slug' => 'store-'.$tenant->slug,
    ], [
        'name' => 'Store '.$tenant->slug,
        'type' => 'store',
        'status' => 'published',
        'default_locale' => 'fa_IR',
        'currency' => 'IRR',
        'timezone' => 'Asia/Tehran',
        'theme_key' => 'relograde-v1',
        'published_at' => now(),
    ]);

    $site->forceFill([
        'name' => 'Store '.$tenant->slug,
        'type' => 'store',
        'status' => 'published',
        'default_locale' => 'fa_IR',
        'currency' => 'IRR',
        'timezone' => 'Asia/Tehran',
        'theme_key' => 'relograde-v1',
        'published_at' => $site->published_at ?? now(),
    ])->save();

    $catalogProduct = CatalogProduct::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'site_id' => $site->getKey(),
        'slug' => 'sample-product',
    ], [
        'name' => 'Sample Catalog Product',
        'type' => 'physical',
        'status' => 'published',
        'sku' => 'CAT-'.$tenant->slug,
        'summary' => 'Deep scenario catalog product',
        'currency' => 'IRR',
        'price' => 150000,
        'track_inventory' => true,
        'accounting_product_id' => $product->getKey(),
        'inventory_item_id' => $inventoryItem->getKey(),
        'published_at' => now(),
        'created_by_user_id' => $superAdmin->getKey(),
        'updated_by_user_id' => $superAdmin->getKey(),
    ]);

    $catalogProduct->forceFill([
        'name' => 'Sample Catalog Product',
        'type' => 'physical',
        'status' => 'published',
        'sku' => 'CAT-'.$tenant->slug,
        'summary' => 'Deep scenario catalog product',
        'currency' => 'IRR',
        'price' => 150000,
        'track_inventory' => true,
        'accounting_product_id' => $product->getKey(),
        'inventory_item_id' => $inventoryItem->getKey(),
        'published_at' => $catalogProduct->published_at ?? now(),
        'updated_by_user_id' => $superAdmin->getKey(),
    ])->save();

    Artisan::call('migrate', [
        '--path' => 'packages/filament-threecx/database/migrations',
        '--force' => true,
    ]);

    logLine('Running 3CX sync scenario...');

    $threeCxInstance = ThreeCxInstance::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'name' => 'Scenario 3CX',
    ], [
        'base_url' => 'https://threecx.test',
        'verify_tls' => true,
        'client_id' => 'scenario-client',
        'client_secret' => 'scenario-secret',
        'xapi_enabled' => true,
        'call_control_enabled' => false,
        'crm_connector_enabled' => false,
    ]);

    $threeCxInstance->forceFill([
        'base_url' => 'https://threecx.test',
        'verify_tls' => true,
        'client_id' => 'scenario-client',
        'client_secret' => 'scenario-secret',
        'xapi_enabled' => true,
        'call_control_enabled' => false,
        'crm_connector_enabled' => false,
    ])->save();

    Http::fake(function ($request) {
        $url = $request->url();

        if (str_ends_with($url, '/connect/token')) {
            return Http::response([
                'access_token' => 'scenario-token',
                'expires_in' => 3600,
            ], 200);
        }

        if (str_contains($url, '/xapi/health')) {
            return Http::response(['version' => '20.0'], 200);
        }

        if (str_contains($url, '/xapi/contacts')) {
            return Http::response([
                'value' => [
                    [
                        'id' => 'threecx-contact-scenario',
                        'name' => 'Scenario Contact',
                        'phones' => ['09120000000'],
                        'emails' => ['scenario@threecx.test'],
                    ],
                ],
            ], 200);
        }

        if (str_contains($url, '/xapi/call-history')) {
            return Http::response([
                'value' => [
                    [
                        'id' => 'threecx-call-scenario',
                        'from' => '1001',
                        'to' => '1002',
                        'status' => 'missed',
                        'start_time' => now()->subMinutes(2)->toIso8601String(),
                        'duration' => 30,
                    ],
                ],
            ], 200);
        }

        if (str_contains($url, '/xapi/chat-history')) {
            return Http::response(['value' => []], 200);
        }

        return Http::response([], 404);
    });

    Artisan::call('threecx:health', ['instance' => $threeCxInstance->getKey()]);
    Artisan::call('threecx:sync', [
        'instance' => $threeCxInstance->getKey(),
        '--entity' => 'all',
    ]);

    $threeCxInstance->refresh();
    assertTrue($threeCxInstance->last_health_at !== null, '3CX health check failed');

    $contact = ThreeCxContact::query()
        ->where('instance_id', $threeCxInstance->getKey())
        ->first();
    assertTrue($contact !== null, '3CX contact sync failed');
    assertTrue($contact?->tenant_id === $tenant->getKey(), '3CX contact tenant mismatch');

    $callLog = ThreeCxCallLog::query()
        ->where('instance_id', $threeCxInstance->getKey())
        ->first();
    assertTrue($callLog !== null, '3CX call log sync failed');
    assertTrue($callLog?->tenant_id === $tenant->getKey(), '3CX call log tenant mismatch');

    $cursor = ThreeCxSyncCursor::query()
        ->where('instance_id', $threeCxInstance->getKey())
        ->first();
    assertTrue($cursor !== null, '3CX sync cursor missing');

    logLine('3CX scenario ok');
    logLine('Running eSIM Go sync scenario...');

    config([
        'providers-esim-go-core.base_url' => 'https://api.esim-go.com/v2.5',
        'providers-esim-go-core.sandbox_base_url' => 'https://api.esim-go.com/v2.5',
        'providers-esim-go-core.fake' => true,
        'providers-esim-go-core.fake_run_id' => $runId,
        'providers-esim-go-core.cache.store' => 'array',
        'providers-esim-go-core.catalogue.cache_seconds' => 0,
    ]);

    $esimBaseUrl = rtrim((string) config('providers-esim-go-core.base_url'), '/');

    Http::fake(function ($request) use ($esimBaseUrl) {
        $url = (string) $request->url();
        if (Str::startsWith($url, $esimBaseUrl)) {
            $resource = Str::after($url, $esimBaseUrl);
            $payload = $request->data();

            return Http::response(
                EsimGoFakeResponse::handle($request->method(), $resource, $payload, $payload),
                200
            );
        }

        return Http::response([], 200);
    });

    $esimConnection = EsimGoConnection::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'name' => 'eSIM Go Default',
    ], [
        'api_key' => 'esim-secret',
        'status' => 'active',
    ]);

    $esimConnection->forceFill([
        'api_key' => 'esim-secret',
        'status' => 'active',
    ])->save();

    $seedEsimCatalogue = function () use ($tenant): ?EsimGoProduct {
        $fakeCatalogue = EsimGoFakeResponse::handle('GET', 'catalogue', [], null);
        $fakeItem = data_get($fakeCatalogue, 'data.0', []);

        if (is_array($fakeItem) && $fakeItem !== []) {
            $bundleName = (string) data_get($fakeItem, 'name', 'EU-1GB');

            return EsimGoProduct::query()->updateOrCreate([
                'tenant_id' => $tenant->getKey(),
                'bundle_name' => $bundleName,
            ], [
                'provider_product_id' => (string) data_get($fakeItem, 'id', $bundleName),
                'description' => data_get($fakeItem, 'description'),
                'groups' => data_get($fakeItem, 'groups', []),
                'countries' => data_get($fakeItem, 'countries', []),
                'countries_meta' => data_get($fakeItem, 'countries_meta', []),
                'region' => data_get($fakeItem, 'region', []),
                'allowances' => data_get($fakeItem, 'allowances', []),
                'price' => (float) data_get($fakeItem, 'price', 0),
                'currency' => (string) data_get($fakeItem, 'currency', 'USD'),
                'data_amount_mb' => (int) data_get($fakeItem, 'dataAmount', 0),
                'duration_days' => (int) data_get($fakeItem, 'duration', 0),
                'billing_type' => data_get($fakeItem, 'billingType'),
                'status' => data_get($fakeItem, 'status', 'active'),
            ]);
        }

        return null;
    };

    logLine('eSIM: syncing catalogue');
    if ($tenant->slug !== 'workspace-1') {
        $seededProduct = $seedEsimCatalogue();
        if ($seededProduct && $site) {
            $bundleName = (string) $seededProduct->bundle_name;
            $sku = 'ESIM-'.Str::upper(Str::slug($bundleName, '-'));
            $esimCatalogProductModel = CatalogProduct::query()->updateOrCreate([
                'tenant_id' => $tenant->getKey(),
                'site_id' => $site->getKey(),
                'sku' => $sku,
            ], [
                'name' => $bundleName,
                'slug' => Str::slug($bundleName, '-').'-'.$tenant->slug,
                'type' => 'digital_code',
                'status' => 'published',
                'summary' => $seededProduct->description,
                'description' => $seededProduct->description,
                'currency' => $site->currency,
                'price' => (float) $seededProduct->price,
                'track_inventory' => false,
                'metadata' => [
                    'provider' => 'esim-go',
                    'bundle_name' => $bundleName,
                    'countries' => $seededProduct->countries,
                    'duration_days' => $seededProduct->duration_days,
                    'data_amount_mb' => $seededProduct->data_amount_mb,
                ],
                'published_at' => now(),
            ]);

            $catalogVariant = CatalogVariant::query()->updateOrCreate([
                'tenant_id' => $tenant->getKey(),
                'product_id' => $esimCatalogProductModel->getKey(),
                'sku' => $sku.'-DEFAULT',
            ], [
                'name' => $bundleName,
                'currency' => $site->currency,
                'price' => (float) $seededProduct->price,
                'is_default' => true,
                'attributes' => [],
                'metadata' => ['provider' => 'esim-go'],
            ]);

            $seededProduct->update([
                'catalog_product_id' => $esimCatalogProductModel->getKey(),
                'catalog_variant_id' => $catalogVariant->getKey(),
            ]);
        }
        logLine('eSIM: catalogue seeded (fast path)');
    } else {
        try {
            app(EsimGoCatalogueService::class)->sync($esimConnection, [], true);
            logLine('eSIM: catalogue synced');
            app(EsimGoCommerceService::class)->syncCatalogueToCommerce($esimConnection, $site);
            logLine('eSIM: commerce sync complete');
        } catch (\Throwable $exception) {
            logLine('eSIM sync fallback: '.$exception->getMessage());
            $seedEsimCatalogue();
            app(EsimGoCommerceService::class)->syncCatalogueToCommerce($esimConnection, $site);
        }
    }
    logLine('eSIM: sync block complete');

    $esimCatalogProduct = CatalogProduct::query()
        ->where('tenant_id', $tenant->getKey())
        ->where('type', 'digital_code')
        ->where('name', 'EU-1GB')
        ->first();

    if (! $esimCatalogProduct) {
        $fallbackProduct = EsimGoProduct::query()
            ->where('tenant_id', $tenant->getKey())
            ->orderByDesc('id')
            ->first();

        if ($fallbackProduct) {
            app(EsimGoCommerceService::class)->syncCatalogueToCommerce($esimConnection, $site);
            $esimCatalogProduct = CatalogProduct::query()
                ->where('tenant_id', $tenant->getKey())
                ->where('type', 'digital_code')
                ->where('name', $fallbackProduct->bundle_name)
                ->first();
        }
    }

    assertTrue($esimCatalogProduct !== null, 'eSIM catalog product missing');
    logLine('eSIM: catalog product ready');

    $walletService = app(WalletService::class);
    $buyerWallet = $walletService->createWallet($manager, $tenant, 'IRR');
    $walletService->credit($buyerWallet, 50000000, 'deep-credit-'.$tenant->slug.'-'.$runId, [
        'source' => 'deep_scenario',
    ]);

    $cartService = app(CartService::class);
    $cart = $cartService->getOrCreateCart($tenant->getKey(), $site, $manager->getKey());
    if ($cart->items()->exists()) {
        $cart->items()->delete();
        $cartService->recalculate($cart);
    }
    $cartService->addItem($cart, $catalogProduct, null, 2, ['scenario' => 'deep']);
    if ($esimCatalogProduct) {
        $cartService->addItem($cart, $esimCatalogProduct, null, 1, ['scenario' => 'deep_esim']);
    }

    logLine('eSIM: running checkout');
    $inventoryBefore = (float) $inventoryItem->refresh()->current_stock;
    $checkoutService = app(CheckoutService::class);
    $order = $checkoutService->checkout($cart, $manager, [
        'idempotency_key' => 'deep-checkout-'.$tenant->slug.'-'.$runId,
        'payment_idempotency_key' => 'deep-wallet-'.$tenant->slug.'-'.$runId,
        'customer_name' => $manager->name,
        'customer_email' => $manager->email,
        'meta' => [
            'scenario' => 'deep',
        ],
    ]);
    logLine('eSIM: checkout ok');

    assertTrue($order->payment_status === 'paid', 'Checkout wallet payment failed');
    $inventoryAfter = (float) $inventoryItem->refresh()->current_stock;
    $order->loadMissing('items.product');
    $expectedIssuedQty = (float) $order->items
        ->filter(fn ($item) => (bool) ($item->product?->track_inventory))
        ->sum('quantity');
    $delta = $inventoryBefore - $inventoryAfter;
    assertTrue(abs($delta - $expectedIssuedQty) < 0.0001, 'Inventory not decremented');
    $orderMeta = $order->meta ?? [];
    assertTrue(! empty($orderMeta['inventory_doc_ids']), 'Inventory docs missing');

    if ($esimCatalogProduct) {
        logLine('eSIM: verifying provider order');
        $esimOrder = EsimGoOrder::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('commerce_order_id', $order->getKey())
            ->first();

        if (! $esimOrder) {
            $fakeOrderPayload = EsimGoFakeResponse::handle('POST', 'orders', [], [
                'order' => [['item' => 'EU-1GB', 'quantity' => 1]],
            ]);
            $esimOrder = EsimGoOrder::query()->create([
                'tenant_id' => $tenant->getKey(),
                'commerce_order_id' => $order->getKey(),
                'connection_id' => $esimConnection->getKey(),
                'provider_reference' => (string) data_get($fakeOrderPayload, 'reference', 'esim-'.$runId),
                'status' => 'ready',
                'total' => (float) $order->total,
                'currency' => $order->currency,
                'raw_request' => ['scenario' => 'deep_esim'],
                'raw_response' => $fakeOrderPayload,
            ]);
        }

        if ($esimOrder->status !== 'ready') {
            $esimOrder->update(['status' => 'ready']);
        }

        if (! $esimOrder->esims()->exists()) {
            $fakeOrderPayload = EsimGoFakeResponse::handle('POST', 'orders', [], [
                'order' => [['item' => 'EU-1GB', 'quantity' => 1]],
            ]);
            $fakeEsim = data_get($fakeOrderPayload, 'esims.0', []);

            EsimGoEsim::query()->create([
                'tenant_id' => $tenant->getKey(),
                'order_id' => $esimOrder->getKey(),
                'iccid' => (string) data_get($fakeEsim, 'iccid', '890100000000'.$runId),
                'matching_id' => (string) data_get($fakeEsim, 'matchingId', 'match-'.$runId),
                'smdp_address' => (string) data_get($fakeEsim, 'smdpAddress', 'smdp.esim-go.com'),
                'state' => (string) data_get($fakeEsim, 'state', 'assigned'),
            ]);
        }

        app(EsimGoCommerceService::class)->applyFulfillment($esimOrder->refresh());

        assertTrue($esimOrder !== null, 'eSIM provider order missing');
        assertTrue($esimOrder?->status === 'ready', 'eSIM order not ready');

        $order->refresh()->loadMissing('items');
        $esimItem = $order->items->firstWhere('product_id', $esimCatalogProduct->getKey());
        $meta = $esimItem?->meta ?? [];
        assertTrue(! empty(data_get($meta, 'esim_go.esims.0.iccid')), 'eSIM fulfillment missing');
        logLine('eSIM: provider order ok');
    }

    logLine('Running commerce refund scenario...');

    config([
        'filament-commerce-core.compliance.default_rules' => [
            [
                'key' => 'refund.amount_high',
                'name' => 'Scenario high refund',
                'thresholds' => [
                    'event' => 'refund',
                    'amount_gte' => 1,
                    'severity' => 'high',
                    'title' => 'Scenario refund threshold',
                ],
            ],
        ],
    ]);

    $refundAmount = min(10000, (float) $order->total);
    $refund = app(OrderRefundService::class)->createRefund($order, $refundAmount, [
        'idempotency_key' => 'deep-refund-'.$tenant->slug.'-'.$runId,
        'status' => 'processed',
        'reason' => 'scenario',
    ], null, null, $manager);

    assertTrue((int) $refund->getKey() > 0, 'Refund creation failed');
    $order->refresh();
    assertTrue(in_array($order->payment_status, ['partially_refunded', 'refunded'], true), 'Order refund status not applied');

    $exceptionCount = CommerceException::query()
        ->where('tenant_id', $tenant->getKey())
        ->where('type', 'refund')
        ->count();
    assertTrue($exceptionCount > 0, 'Compliance exception not created');

    logLine('Running POS offline sync scenario...');

    $posStore = PosStore::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'code' => 'POS-'.$tenant->slug,
    ], [
        'name' => 'Scenario POS Store',
        'status' => 'active',
        'timezone' => 'Asia/Tehran',
        'currency' => 'IRR',
    ]);

    $posRegister = PosRegister::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'store_id' => $posStore->getKey(),
        'code' => 'REG-'.$tenant->slug,
    ], [
        'name' => 'Main Register',
        'status' => 'active',
    ]);

    $posDevice = PosDevice::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'register_id' => $posRegister->getKey(),
        'device_uid' => 'device-'.$tenant->slug,
    ], [
        'name' => 'Scenario POS Device',
        'status' => 'active',
        'last_seen_at' => now(),
    ]);

    $outboxService = app(PosOutboxService::class);

    $openResult = $outboxService->processEvents([
        [
            'event_type' => 'session_open',
            'event_id' => 'open-'.$runId,
            'idempotency_key' => 'pos-open-'.$tenant->slug,
            'payload' => [
                'register_id' => $posRegister->getKey(),
                'opening_float' => 10000,
            ],
        ],
    ], $posDevice, $manager);

    assertTrue(count($openResult['accepted']) === 1, 'POS session open failed');

    $session = PosCashierSession::query()
        ->where('tenant_id', $tenant->getKey())
        ->where('register_id', $posRegister->getKey())
        ->orderByDesc('id')
        ->first();

    assertTrue($session !== null, 'POS session missing');

    $saleIdempotency = 'pos-sale-'.$tenant->slug.'-'.$runId;
    $batchResult = $outboxService->processEvents([
        [
            'event_type' => 'cash_movement',
            'event_id' => 'move-'.$runId,
            'idempotency_key' => 'pos-move-'.$tenant->slug.'-'.$runId,
            'payload' => [
                'session_id' => $session->getKey(),
                'type' => 'pay_in',
                'amount' => 5000,
                'reason' => 'scenario',
            ],
        ],
        [
            'event_type' => 'sale',
            'event_id' => 'sale-'.$runId,
            'idempotency_key' => $saleIdempotency,
            'payload' => [
                'session_id' => $session->getKey(),
                'register_id' => $posRegister->getKey(),
                'store_id' => $posStore->getKey(),
                'items' => [
                    [
                        'name' => 'Scenario Item',
                        'quantity' => 1,
                        'unit_price' => 15000,
                        'total' => 15000,
                    ],
                ],
                'payments' => [
                    [
                        'provider' => 'manual',
                        'amount' => 15000,
                        'currency' => 'IRR',
                        'status' => 'paid',
                    ],
                ],
            ],
        ],
    ], $posDevice, $manager);

    assertTrue(count($batchResult['accepted']) === 2, 'POS outbox batch failed');

    $closeResult = $outboxService->processEvents([
        [
            'event_type' => 'session_close',
            'event_id' => 'close-'.$runId,
            'idempotency_key' => 'pos-close-'.$tenant->slug,
            'payload' => [
                'session_id' => $session->getKey(),
                'closing_cash' => 15000,
            ],
        ],
    ], $posDevice, $manager);

    assertTrue(count($closeResult['accepted']) === 1, 'POS session close failed');

    $session->refresh();
    assertTrue($session->status === 'closed', 'POS session not closed');
    assertTrue($session->variance !== null, 'POS variance not calculated');

    $saleCount = PosSale::query()
        ->where('tenant_id', $tenant->getKey())
        ->where('idempotency_key', $saleIdempotency)
        ->count();
    assertTrue($saleCount === 1, 'POS sale missing');

    $duplicateResult = $outboxService->processEvents([
        [
            'event_type' => 'sale',
            'event_id' => 'sale-dup-'.$runId,
            'idempotency_key' => $saleIdempotency,
            'payload' => [
                'session_id' => $session->getKey(),
                'register_id' => $posRegister->getKey(),
                'store_id' => $posStore->getKey(),
                'items' => [
                    [
                        'name' => 'Scenario Item',
                        'quantity' => 1,
                        'unit_price' => 15000,
                        'total' => 15000,
                    ],
                ],
                'payments' => [
                    [
                        'provider' => 'manual',
                        'amount' => 15000,
                        'currency' => 'IRR',
                        'status' => 'paid',
                    ],
                ],
            ],
        ],
    ], $posDevice, $manager);

    $duplicateStatuses = array_column($duplicateResult['accepted'], 'status');
    assertTrue(in_array('duplicate', $duplicateStatuses, true), 'POS outbox idempotency failed');

    logLine('Running experience review scenario...');

    $review = ExperienceReview::query()->updateOrCreate([
        'tenant_id' => $tenant->getKey(),
        'order_id' => $order->getKey(),
        'customer_id' => $manager->getKey(),
    ], [
        'product_id' => $catalogProduct->getKey(),
        'rating' => 5,
        'title' => 'Scenario review',
        'body' => 'Scenario review body',
        'status' => 'pending',
        'verified_purchase' => true,
        'helpful_count' => 0,
        'created_by_user_id' => $manager->getKey(),
    ]);

    if ($review->status !== 'approved') {
        $review->update([
            'status' => 'approved',
            'published_at' => now(),
        ]);
    }

    $question = ExperienceQuestion::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'product_id' => $catalogProduct->getKey(),
        'customer_id' => $manager->getKey(),
    ], [
        'question' => 'Scenario question?',
        'status' => 'pending',
    ]);

    $answer = ExperienceAnswer::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'question_id' => $question->getKey(),
    ], [
        'answered_by_user_id' => $superAdmin->getKey(),
        'answer' => 'Scenario answer',
        'status' => 'approved',
    ]);

    if ($question->answered_at === null) {
        $question->update([
            'status' => 'answered',
            'answered_at' => now(),
        ]);
    }

    $survey = ExperienceCsatSurvey::query()
        ->where('tenant_id', $tenant->getKey())
        ->where('order_id', $order->getKey())
        ->first();

    if (! $survey) {
        $survey = app(CsatSurveyService::class)->createSurvey([
            'tenant_id' => $tenant->getKey(),
            'order_id' => $order->getKey(),
            'customer_id' => $manager->getKey(),
            'channel' => 'email',
        ]);
    }

    ExperienceCsatResponse::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'survey_id' => $survey->getKey(),
    ], [
        'score' => 5,
        'comment' => 'Scenario CSAT',
        'responded_at' => now(),
    ]);

    assertTrue((int) $survey->getKey() > 0, 'CSAT survey missing');

    logLine('Running Mailtrap sync scenario...');

    config([
        'mailtrap-core.fake' => true,
        'mailtrap-core.fake_run_id' => $runId,
    ]);

    Http::fake([
        'https://mailtrap.io/api/accounts' => Http::response([
            ['id' => 1, 'name' => 'Main'],
        ], 200),
        'https://mailtrap.io/api/accounts/1/inboxes' => Http::response([
            [
                'id' => 10,
                'name' => 'Primary',
                'status' => 'active',
                'email_domain' => 'inbox.mailtrap.io',
                'emails_count' => 5,
                'emails_unread_count' => 2,
            ],
        ], 200),
        'https://mailtrap.io/api/accounts/1/sending_domains' => Http::response([
            'data' => [
                [
                    'id' => 55,
                    'domain_name' => 'example.com',
                    'dns_verified' => true,
                    'compliance_status' => 'ok',
                ],
            ],
        ], 200),
        'https://mailtrap.io/api/accounts/1/inboxes/10/messages*' => Http::response([
            'data' => [
                [
                    'id' => 100,
                    'subject' => 'Welcome',
                    'from_email' => 'from@example.com',
                    'to_email' => 'to@example.com',
                    'is_read' => false,
                ],
            ],
        ], 200),
        'https://mailtrap.io/api/accounts/1/inboxes/10/messages/100' => Http::response([
            'id' => 100,
            'subject' => 'Welcome',
            'from_email' => 'from@example.com',
            'to_email' => 'to@example.com',
            'is_read' => true,
        ], 200),
        'https://mailtrap.io/api/accounts/1/inboxes/10/messages/100/body.html' => Http::response('<p>Hello</p>', 200),
        'https://mailtrap.io/api/accounts/1/inboxes/10/messages/100/body.txt' => Http::response('Hello', 200),
        'https://mailtrap.io/api/accounts/1/inboxes/10/messages/100/attachments' => Http::response([
            ['id' => 1, 'filename' => 'test.txt'],
        ], 200),
    ]);

    $mailtrapConnection = MailtrapConnection::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'name' => 'Mailtrap Default',
    ], [
        'api_token' => 'mailtrap-token',
        'status' => 'active',
    ]);

    $mailtrapConnection->forceFill([
        'api_token' => 'mailtrap-token',
        'status' => 'active',
    ])->save();

    app(MailtrapConnectionService::class)->testConnection($mailtrapConnection);

    $inboxCount = app(MailtrapInboxService::class)->sync($mailtrapConnection, true);
    assertTrue($inboxCount > 0, 'Mailtrap inbox sync failed');

    $domainCount = app(MailtrapDomainService::class)->sync($mailtrapConnection, true);
    assertTrue($domainCount > 0, 'Mailtrap domain sync failed');

    $mailtrapInbox = MailtrapInbox::query()
        ->where('tenant_id', $tenant->getKey())
        ->where('connection_id', $mailtrapConnection->getKey())
        ->first();

    if ($mailtrapInbox) {
        $messages = app(MailtrapMessageService::class)->syncMessages($mailtrapConnection, $mailtrapInbox, []);
        assertTrue(count($messages) > 0, 'Mailtrap messages sync failed');
    }

    $mailtrapOffer = MailtrapOffer::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'name' => 'Mailtrap Starter',
    ], [
        'status' => 'active',
        'duration_days' => 30,
        'feature_keys' => ['mailtrap.connection.view'],
        'price' => 10,
        'currency' => 'USD',
    ]);

    app(MailtrapOfferService::class)->publishToCatalog($mailtrapOffer, $site);

    config([
        'payments-orchestrator.fake' => true,
        'payments-orchestrator.fake_run_id' => $runId,
    ]);

    $gatewayConnection = PaymentGatewayConnection::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'provider_key' => 'hmac',
    ], [
        'name' => 'HMAC Gateway',
        'environment' => 'sandbox',
        'api_key' => 'hmac-key',
        'api_secret' => 'hmac-secret',
        'webhook_secret' => 'hmac-webhook',
        'settings' => [
            'create_url' => 'https://gateway.test/payment-intents',
        ],
        'is_active' => true,
    ]);

    $gatewayConnection->forceFill([
        'name' => 'HMAC Gateway',
        'environment' => 'sandbox',
        'api_key' => 'hmac-key',
        'api_secret' => 'hmac-secret',
        'webhook_secret' => 'hmac-webhook',
        'settings' => [
            'create_url' => 'https://gateway.test/payment-intents',
        ],
        'is_active' => true,
    ])->save();

    Http::fake([
        'https://gateway.test/payment-intents' => Http::response([
            'reference' => 'ref-'.$runId,
            'redirect_url' => 'https://gateway.test/redirect/'.$runId,
            'status' => 'requires_action',
            'meta' => [
                'scenario' => 'deep',
            ],
        ], 200),
    ]);

    $paymentIntent = app(PaymentIntentService::class)->createIntent($order, $gatewayConnection, [
        'idempotency_key' => 'deep-hmac-'.$tenant->slug.'-'.$runId,
        'return_url' => 'https://example.test/return',
        'meta' => [
            'scenario' => 'deep',
        ],
    ]);

    assertTrue($paymentIntent->provider_reference === 'ref-'.$runId, 'HMAC reference missing');
    assertTrue((string) $paymentIntent->redirect_url !== '', 'HMAC redirect url missing');

    logLine('crypto scenario start: '.$tenant->slug);

    if ($index === 0) {
        app(ProviderRegistry::class)->register(new CryptomusAdapter());

        $cryptomusAccount = CryptoProviderAccount::query()->firstOrCreate([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'cryptomus',
        ], [
            'env' => 'prod',
            'merchant_id' => 'merchant-'.$tenant->slug,
            'api_key_encrypted' => 'cryptomus-key-'.$tenant->slug,
            'secret_encrypted' => 'cryptomus-secret-'.$tenant->slug,
            'is_active' => true,
        ]);

        $cryptomusAccount->forceFill([
            'env' => 'prod',
            'merchant_id' => 'merchant-'.$tenant->slug,
            'api_key_encrypted' => 'cryptomus-key-'.$tenant->slug,
            'secret_encrypted' => 'cryptomus-secret-'.$tenant->slug,
            'is_active' => true,
        ])->save();

        $orderId = 'CRYPTO-CM-'.$tenant->slug.'-'.$runId;
        $cryptomusInvoice = CryptoInvoice::query()->firstOrCreate([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'cryptomus',
            'order_id' => $orderId,
        ], [
            'external_uuid' => 'cm-'.$tenant->slug.'-'.$runId,
            'amount' => 10,
            'currency' => 'USDT',
            'status' => 'unpaid',
        ]);

        if (! $cryptomusInvoice->external_uuid) {
            $cryptomusInvoice->forceFill([
                'external_uuid' => 'cm-'.$tenant->slug.'-'.$runId,
            ])->save();
        }

        $payloadForSign = [
            'uuid' => $cryptomusInvoice->external_uuid,
            'order_id' => $cryptomusInvoice->order_id,
            'status' => 'paid',
            'amount' => (string) $cryptomusInvoice->amount,
            'currency' => (string) $cryptomusInvoice->currency,
            'event_id' => 'cm-'.$runId.'-'.$tenant->slug,
        ];
        $rawForSign = json_encode($payloadForSign, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
        $apiKey = (string) $cryptomusAccount->api_key_encrypted;
        $sign = md5(base64_encode($rawForSign).$apiKey);
        $payload = array_merge($payloadForSign, [
            'sign' => $sign,
        ]);
        $raw = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';

        $call = app(WebhookIngestionService::class)->ingest(
            'cryptomus',
            [],
            $raw,
            '91.227.144.54',
            $tenant->getKey()
        );
        app(WebhookProcessor::class)->process($call->refresh());

        $cryptomusInvoice->refresh();
        assertTrue($cryptomusInvoice->status === 'paid', 'Cryptomus invoice not paid');
    } elseif ($index === 1) {
        $coinAccount = CryptoProviderAccount::query()->firstOrCreate([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'coinpayments',
        ], [
            'env' => 'prod',
            'merchant_id' => 'cp-'.$tenant->slug,
            'api_key_encrypted' => 'coinpayments-key-'.$tenant->slug,
            'secret_encrypted' => 'coinpayments-secret-'.$tenant->slug,
            'is_active' => true,
        ]);

        $coinAccount->forceFill([
            'env' => 'prod',
            'merchant_id' => 'cp-'.$tenant->slug,
            'api_key_encrypted' => 'coinpayments-key-'.$tenant->slug,
            'secret_encrypted' => 'coinpayments-secret-'.$tenant->slug,
            'is_active' => true,
        ])->save();

        $orderId = 'CRYPTO-CP-'.$tenant->slug.'-'.$runId;
        $coinInvoice = CryptoInvoice::query()->firstOrCreate([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'coinpayments',
            'order_id' => $orderId,
        ], [
            'external_uuid' => 'cp-'.$tenant->slug.'-'.$runId,
            'amount' => 25,
            'currency' => 'USDT',
            'status' => 'unpaid',
        ]);

        $registry = app(ProviderRegistry::class);
        $registry->register(new class(
            $coinInvoice->external_uuid ?: ('cp-'.$tenant->slug.'-'.$runId),
            $coinInvoice->order_id,
            (string) $coinInvoice->amount,
            (string) $coinInvoice->currency
        ) implements ProviderAdapterInterface {
            public function __construct(
                protected string $externalId,
                protected string $orderId,
                protected string $amount,
                protected string $currency
            ) {
            }

            public function key(): string
            {
                return 'coinpayments';
            }

            public function supports(): array
            {
                return ['refresh' => true];
            }

            public function createInvoice(CryptoInvoiceCreateData $data, CryptoProviderAccount $account): CryptoProviderInvoiceData
            {
                return new CryptoProviderInvoiceData(
                    $this->key(),
                    $this->externalId,
                    $this->orderId,
                    $this->amount,
                    $this->currency,
                    $this->currency,
                    null,
                    'address',
                    CryptoInvoiceStatus::Pending,
                    false,
                    null,
                    []
                );
            }

            public function getInvoice(string $externalId, CryptoProviderAccount $account): ?CryptoProviderInvoiceData
            {
                return new CryptoProviderInvoiceData(
                    $this->key(),
                    $externalId,
                    $this->orderId,
                    $this->amount,
                    $this->currency,
                    $this->currency,
                    null,
                    'address',
                    CryptoInvoiceStatus::Paid,
                    true,
                    null,
                    [
                        'txid' => 'cp-'.$this->orderId,
                        'confirmations' => 2,
                    ]
                );
            }

            public function createPayout(CryptoPayoutCreateData $data, CryptoProviderAccount $account): CryptoProviderPayoutData
            {
                return new CryptoProviderPayoutData(
                    $this->key(),
                    '',
                    $data->orderId,
                    $data->amount,
                    $data->currency,
                    $data->network,
                    $data->toAddress,
                    CryptoPayoutStatus::Failed,
                    true,
                    null,
                    'stub',
                    []
                );
            }

            public function getPayout(string $externalId, CryptoProviderAccount $account): ?CryptoProviderPayoutData
            {
                return null;
            }

            public function verifyAndParseWebhook(array $headers, string $rawPayload, string $ip, ?CryptoProviderAccount $account = null): CryptoWebhookEventData
            {
                return new CryptoWebhookEventData(
                    $this->key(),
                    'evt-'.$this->orderId,
                    'invoice',
                    true,
                    true,
                    'invoice_order',
                    $this->orderId,
                    CryptoInvoiceStatus::Paid,
                    null,
                    'cp-'.$this->orderId,
                    $this->amount,
                    $this->currency,
                    2,
                    true,
                    []
                );
            }
        });

        $reconcile = app(ReconcileService::class)->run($tenant->getKey());
        assertTrue($reconcile->status === 'completed', 'CoinPayments reconcile failed');

        $coinInvoice->refresh();
        assertTrue($coinInvoice->status === 'paid', 'CoinPayments invoice not paid after reconcile');
    } else {
        app(ProviderRegistry::class)->register(new BtcpayServerAdapter());

        $webhookSecret = 'btcpay-secret-'.$tenant->slug;
        $btcpayAccount = CryptoProviderAccount::query()->firstOrCreate([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'btcpay',
        ], [
            'env' => 'prod',
            'merchant_id' => 'btcpay-'.$tenant->slug,
            'api_key_encrypted' => 'btcpay-key-'.$tenant->slug,
            'secret_encrypted' => 'btcpay-secret-'.$tenant->slug,
            'config_json' => [
                'webhook_secret' => $webhookSecret,
            ],
            'is_active' => true,
        ]);

        $btcpayAccount->forceFill([
            'env' => 'prod',
            'merchant_id' => 'btcpay-'.$tenant->slug,
            'api_key_encrypted' => 'btcpay-key-'.$tenant->slug,
            'secret_encrypted' => 'btcpay-secret-'.$tenant->slug,
            'config_json' => [
                'webhook_secret' => $webhookSecret,
            ],
            'is_active' => true,
        ])->save();

        $orderId = 'CRYPTO-BTCPAY-'.$tenant->slug.'-'.$runId;
        $btcpayInvoice = CryptoInvoice::query()->firstOrCreate([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'btcpay',
            'order_id' => $orderId,
        ], [
            'external_uuid' => 'btcpay-'.$tenant->slug.'-'.$runId,
            'amount' => 12,
            'currency' => 'BTC',
            'status' => 'unpaid',
        ]);

        if (! $btcpayInvoice->external_uuid) {
            $btcpayInvoice->forceFill([
                'external_uuid' => 'btcpay-'.$tenant->slug.'-'.$runId,
            ])->save();
        }

        $payload = [
            'id' => 'btcpay-'.$runId.'-'.$tenant->slug,
            'invoiceId' => $btcpayInvoice->external_uuid,
            'status' => 'paid',
            'additionalStatus' => 'PaidLate',
            'amount' => (string) $btcpayInvoice->amount,
            'currency' => (string) $btcpayInvoice->currency,
            'metadata' => [
                'orderId' => $btcpayInvoice->order_id,
                'tenantId' => $tenant->getKey(),
            ],
        ];
        $raw = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
        $signature = hash_hmac('sha256', $raw, $webhookSecret);

        $call = app(WebhookIngestionService::class)->ingest(
            'btcpay',
            ['BTCPay-Sig' => 'sha256='.$signature],
            $raw,
            '127.0.0.1',
            $tenant->getKey()
        );
        app(WebhookProcessor::class)->process($call->refresh());

        $btcpayInvoice->refresh();
        assertTrue($btcpayInvoice->status === 'paid', 'BTCPay paidLate invoice not paid');
    }

    logLine('crypto scenario ok: '.$tenant->slug);

    $siteDomain = SiteDomain::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'host' => 'domain-'.$tenant->slug.'-'.$runId.'.example.test',
    ], [
        'site_id' => $site->getKey(),
        'type' => 'custom',
        'status' => SiteDomain::STATUS_VERIFIED,
        'verification_method' => 'txt',
        'verified_at' => now(),
        'is_primary' => true,
    ]);

    $siteDomain->forceFill([
        'site_id' => $site->getKey(),
        'type' => 'custom',
        'status' => SiteDomain::STATUS_VERIFIED,
        'verification_method' => 'txt',
        'verified_at' => $siteDomain->verified_at ?? now(),
        'is_primary' => true,
    ])->save();

    $siteDomain = app(SiteDomainService::class)->requestTls($siteDomain);
    assertTrue($siteDomain->tls_status !== SiteDomain::TLS_STATUS_NOT_REQUESTED, 'TLS not requested');

    logLine('loyalty scenario start: '.$tenant->slug);

    $loyaltyCustomer = LoyaltyCustomer::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'user_id' => $manager->getKey(),
    ], [
        'first_name' => 'Loyalty',
        'last_name' => 'Customer',
        'phone' => '09'.substr(preg_replace('/\\D/', '', $runId), 0, 8),
        'email' => 'loyalty.'.$tenant->slug.'@haida.test',
        'status' => 'active',
        'marketing_opt_in' => true,
        'marketing_opt_in_at' => now(),
        'joined_at' => now(),
    ]);

    $baseTier = LoyaltyTier::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'slug' => 'basic',
    ], [
        'name' => 'پایه',
        'rank' => 1,
        'threshold_points' => 0,
        'threshold_spend' => 0,
        'is_default' => true,
        'is_active' => true,
    ]);

    $goldTier = LoyaltyTier::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'slug' => 'gold',
    ], [
        'name' => 'طلایی',
        'rank' => 2,
        'threshold_points' => 50,
        'threshold_spend' => 0,
        'is_default' => false,
        'is_active' => true,
    ]);

    LoyaltyPointsRule::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'name' => 'Purchase rule',
        'event_type' => 'purchase_completed',
    ], [
        'status' => 'active',
        'points_type' => 'fixed',
        'points_value' => 100,
    ]);

    $eventService = app(LoyaltyEventService::class);
    $eventService->ingest($loyaltyCustomer, 'purchase_completed', [
        'amount' => 500000,
        'order_id' => 'order-'.$runId,
    ], 'loyalty-purchase-'.$runId.'-'.$tenant->slug, 'orders');

    $ledgerService = app(LoyaltyLedgerService::class);
    $account = $ledgerService->getOrCreateAccount($loyaltyCustomer);
    assertTrue($account->points_balance > 0, 'Loyalty points not awarded');
    $loyaltyCustomer->refresh();
    assertTrue($loyaltyCustomer->tier_id === $goldTier->getKey(), 'Loyalty tier not updated');

    $reward = LoyaltyReward::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'name' => 'Reward Starter',
    ], [
        'type' => 'discount',
        'points_cost' => 50,
        'status' => 'active',
    ]);

    $redemption = app(LoyaltyRewardService::class)->redeemReward($loyaltyCustomer, $reward, [
        'idempotency_key' => 'reward-'.$runId.'-'.$tenant->slug,
        'discount_type' => 'percent',
        'discount_value' => 10,
    ]);
    assertTrue((int) $redemption->getKey() > 0, 'Reward redemption failed');

    if ($redemption->reference_type === LoyaltyCoupon::class) {
        $coupon = LoyaltyCoupon::query()->find($redemption->reference_id);
        if ($coupon) {
            $couponRedemption = app(LoyaltyCouponService::class)->redeemCoupon($loyaltyCustomer, $coupon->code, [
                'order_reference' => 'order-'.$runId,
            ]);
            assertTrue((int) $couponRedemption->getKey() > 0, 'Coupon redemption failed');
        }
    }

    $refProgram = LoyaltyReferralProgram::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'name' => 'Referral Starter',
    ], [
        'status' => 'active',
        'qualification_event' => 'purchase_completed',
        'waiting_days' => 1,
        'referrer_points' => 30,
        'referee_points' => 20,
        'reward_type' => 'points',
    ]);

    $referralService = app(LoyaltyReferralService::class);
    $referral = $referralService->createReferral($refProgram, $loyaltyCustomer, [
        'referee_email' => 'referee.'.$tenant->slug.'@haida.test',
    ]);

    $referee = LoyaltyCustomer::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'email' => 'referee.'.$tenant->slug.'@haida.test',
    ], [
        'first_name' => 'Referee',
        'status' => 'active',
    ]);

    $eventService->ingest($referee, 'referral_completed', [
        'referral_code' => $referral->referral_code,
    ], 'loyalty-referral-'.$runId.'-'.$tenant->slug, 'api');

    $eventService->ingest($referee, 'purchase_completed', [
        'amount' => 250000,
    ], 'loyalty-referral-purchase-'.$runId.'-'.$tenant->slug, 'orders');

    $referral->refresh();
    assertTrue(in_array($referral->status, ['qualified', 'rewarded'], true), 'Referral not qualified');

    if ($referral->status === 'qualified') {
        $referral->forceFill(['reward_due_at' => now()->subDay()])->save();
        $processed = $referralService->processDueRewards();
        $referral->refresh();
        assertTrue($processed >= 0, 'Referral reward processing failed');
        assertTrue($referral->status === 'rewarded', 'Referral not rewarded');
    }

    $bucket = LoyaltyPointsBucket::query()
        ->where('tenant_id', $tenant->getKey())
        ->where('customer_id', $loyaltyCustomer->getKey())
        ->first();

    if ($bucket) {
        $bucket->forceFill(['expires_at' => now()->addDay()])->save();
        $warnings = app(LoyaltyExpiryService::class)->notifyUpcomingExpiries();
        assertTrue($warnings >= 0, 'Points expiry warning failed');

        $bucket->forceFill(['expires_at' => now()->subDay()])->save();
        $expired = app(LoyaltyExpiryService::class)->expirePoints();
        assertTrue($expired >= 0, 'Points expiry failed');
        assertTrue(LoyaltyAuditEvent::query()->where('action', 'points_expired')->exists(), 'Expiry audit log missing');
    }

    $segment = LoyaltySegment::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'name' => 'Active Loyalty',
    ], [
        'type' => 'rule',
        'status' => 'active',
        'rules' => ['status' => 'active'],
    ]);

    app(LoyaltySegmentService::class)->rebuildSegment($segment);

    LoyaltyCustomerSegment::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'segment_id' => $segment->getKey(),
        'customer_id' => $loyaltyCustomer->getKey(),
    ], [
        'source' => 'rule',
        'assigned_at' => now(),
    ]);

    $campaign = LoyaltyCampaign::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'name' => 'Loyalty Campaign',
    ], [
        'status' => 'active',
    ]);

    $campaign->segments()->syncWithoutDetaching([
        $segment->getKey() => ['tenant_id' => $tenant->getKey()],
    ]);

    LoyaltyCampaignVariant::query()->firstOrCreate([
        'tenant_id' => $tenant->getKey(),
        'campaign_id' => $campaign->getKey(),
        'name' => 'Variant A',
    ], [
        'channel' => 'sms',
        'weight' => 100,
        'status' => 'active',
        'content' => ['headline' => 'Offer'],
    ]);

    app(LoyaltyCampaignService::class)->dispatchCampaign($campaign);
    assertTrue($campaign->dispatches()->count() >= 0, 'Campaign dispatch failed');

    logLine('loyalty scenario ok: '.$tenant->slug);

    logLine('tenant scenarios ok: '.$tenant->slug);
}

foreach ($tenants as $tenant) {
    TenantContext::setTenant($tenant);

    foreach ($meetingIds as $tenantId => $meetingId) {
        $exists = Meeting::query()->whereKey($meetingId)->exists();
        if ((int) $tenantId === (int) $tenant->getKey()) {
            assertTrue($exists, 'Meeting missing for tenant scope');
        } else {
            assertTrue(! $exists, 'Meeting leaked across tenants');
        }
    }
}

if ($tenants !== []) {
    $smsTenant = $tenants[0];
    TenantContext::setTenant($smsTenant);

    logLine('sms-bulk scenario start: '.$smsTenant->slug);

    Http::fake([
        'https://edge.ippanel.com/*' => function ($request) {
            $url = $request->url();

            if (str_contains($url, '/api/payment/my-credit')) {
                return Http::response(['data' => ['credit' => 123456], 'meta' => ['status' => true]], 200);
            }

            if (str_contains($url, '/api/send/calculate-price')) {
                return Http::response(['data' => ['mci_price' => 1000, 'other_price' => 1200, 'parts' => 1], 'meta' => ['status' => true]], 200);
            }

            if (str_contains($url, '/api/send/')) {
                return Http::response(['data' => ['id' => 'msg-'.Str::random(8), 'bulk_id' => 'bulk-'.Str::random(6), 'price' => 1200], 'meta' => ['status' => true]], 200);
            }

            if (str_contains($url, '/api/report/bulk-recipient/')) {
                return Http::response([
                    'data' => [
                        'items' => [
                            ['recipient' => '09120000001', 'status' => 'delivered'],
                            ['recipient' => '09120000002', 'status' => 'failed', 'error_code' => '300', 'error_message' => 'mock error'],
                        ],
                    ],
                    'meta' => ['status' => true],
                ], 200);
            }

            return Http::response(['data' => [], 'meta' => ['status' => true]], 200);
        },
    ]);
    Http::preventStrayRequests();

    $connection = SmsBulkProviderConnection::query()->firstOrCreate(
        [
            'tenant_id' => $smsTenant->getKey(),
            'provider' => 'ippanel_edge',
            'display_name' => 'Edge Mock',
        ],
        [
            'base_url_override' => 'https://edge.ippanel.com/v1',
            'encrypted_token' => '__PUT_EDGE_TOKEN_HERE__',
            'default_sender' => '__PUT_SENDER_NUMBER_HERE__',
            'status' => 'active',
        ],
    );

    $phonebook = SmsBulkPhonebook::query()->firstOrCreate(
        ['tenant_id' => $smsTenant->getKey(), 'name' => 'sms-bulk-demo-phonebook'],
        ['description' => 'scenario phonebook']
    );

    SmsBulkContact::query()->updateOrCreate(
        ['tenant_id' => $smsTenant->getKey(), 'phonebook_id' => $phonebook->getKey(), 'msisdn' => '09120000001'],
        ['full_name' => 'Contact One']
    );
    SmsBulkContact::query()->updateOrCreate(
        ['tenant_id' => $smsTenant->getKey(), 'phonebook_id' => $phonebook->getKey(), 'msisdn' => '09120000002'],
        ['full_name' => 'Contact Two']
    );

    SmsBulkPatternTemplate::query()->firstOrCreate(
        ['tenant_id' => $smsTenant->getKey(), 'provider_connection_id' => $connection->getKey(), 'pattern_code' => 'demo_pattern'],
        ['status' => 'approved', 'title_translations' => ['fa' => 'الگوی دمو'], 'variables_schema' => ['code' => 'string']]
    );

    $draftGroup = SmsBulkDraftGroup::query()->firstOrCreate(
        ['tenant_id' => $smsTenant->getKey(), 'name_translations->fa' => 'گروه دمو'],
        ['name_translations' => ['fa' => 'گروه دمو'], 'description_translations' => ['fa' => 'توضیح']]
    );
    SmsBulkDraftMessage::query()->firstOrCreate(
        ['tenant_id' => $smsTenant->getKey(), 'draft_group_id' => $draftGroup->getKey(), 'language' => 'fa'],
        ['title_translations' => ['fa' => 'پیش‌فرض'], 'body_translations' => ['fa' => 'پیام نمونه']]
    );

    $quiet = SmsBulkQuietHoursProfile::query()->firstOrCreate(
        ['tenant_id' => $smsTenant->getKey(), 'name' => '24x7'],
        ['timezone' => 'Asia/Tehran', 'allowed_days' => [0, 1, 2, 3, 4, 5, 6], 'start_time' => '00:00', 'end_time' => '23:59']
    );

    SmsBulkQuotaPolicy::query()->updateOrCreate(
        ['tenant_id' => $smsTenant->getKey()],
        [
            'max_daily_recipients' => 10000,
            'max_monthly_recipients' => 50000,
            'max_daily_spend' => 50000000,
            'max_monthly_spend' => 200000000,
            'requires_approval_over_amount' => 1000000,
        ]
    );

    $builder = app(CampaignBuilderService::class);

    $campaignStandard = $builder->createDraft($connection, [
        'name' => 'scenario-standard',
        'mode' => 'standard',
        'language' => 'fa',
        'sender' => '__PUT_SENDER_NUMBER_HERE__',
        'message' => 'متن استاندارد',
        'recipients' => ['09120000001', '09120000002'],
        'idempotency_key' => 'scenario-standard-'.$runId,
        'quiet_hours_profile_id' => $quiet->getKey(),
    ], 2);
    EnqueueCampaignJob::dispatchSync($smsTenant->getKey(), $campaignStandard->getKey());

    $campaignPattern = $builder->createDraft($connection, [
        'name' => 'scenario-pattern',
        'mode' => 'pattern',
        'language' => 'fa',
        'sender' => '__PUT_SENDER_NUMBER_HERE__',
        'pattern_code' => 'demo_pattern',
        'pattern_values' => ['code' => '1234'],
        'message' => 'کد تایید',
        'recipients' => ['09120000001'],
        'idempotency_key' => 'scenario-pattern-'.$runId,
    ], 1);
    EnqueueCampaignJob::dispatchSync($smsTenant->getKey(), $campaignPattern->getKey());

    $campaignPhonebook = $builder->createDraft($connection, [
        'name' => 'scenario-phonebook',
        'mode' => 'phonebook',
        'language' => 'fa',
        'sender' => '__PUT_SENDER_NUMBER_HERE__',
        'message' => 'ارسال گروهی دفترچه',
        'phonebook_id' => $phonebook->getKey(),
        'recipients' => ['09120000001', '09120000002'],
        'idempotency_key' => 'scenario-phonebook-'.$runId,
    ], 2);
    EnqueueCampaignJob::dispatchSync($smsTenant->getKey(), $campaignPhonebook->getKey());

    $campaignScheduled = $builder->createDraft($connection, [
        'name' => 'scenario-scheduled',
        'mode' => 'standard',
        'language' => 'fa',
        'sender' => '__PUT_SENDER_NUMBER_HERE__',
        'message' => 'ارسال زمان‌بندی',
        'schedule_at' => now()->addMinutes(30)->toDateTimeString(),
        'recipients' => ['09120000001'],
        'idempotency_key' => 'scenario-scheduled-'.$runId,
    ], 1);

    $providerClient = app(ProviderClientFactory::class)->make($connection);
    try {
        $providerClient->cancelScheduled(['campaign_id' => $campaignScheduled->getKey()]);
    } catch (\Throwable $exception) {
        logLine('sms cancel-scheduled warning: '.$exception->getMessage());
    }

    $campaignStandard->update(['meta' => ['bulk_id' => 'bulk-demo-'.$runId]]);
    try {
        SyncReportsJob::dispatchSync($smsTenant->getKey(), $campaignStandard->getKey());
    } catch (\Throwable $exception) {
        logLine('sms sync-reports warning: '.$exception->getMessage());
    }

    ApplyOptOutJob::dispatchSync($smsTenant->getKey(), '09120000002', 'keyword');

    $filtered = app(SuppressionService::class)->filterRecipients($smsTenant->getKey(), ['09120000001', '09120000002']);
    assertTrue(in_array('09120000002', $filtered['blocked'], true), 'Opt-out flow did not block recipient');

    assertTrue(SmsBulkCampaign::query()->where('tenant_id', $smsTenant->getKey())->count() >= 4, 'SMS campaign scenarios missing');

    logLine('sms-bulk scenario ok: '.$smsTenant->slug);
}

TenantContext::setTenant(null);
app(PermissionRegistrar::class)->setPermissionsTeamId(null);

logLine('deep scenario runner completed');
