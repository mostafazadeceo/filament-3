<?php

declare(strict_types=1);

namespace Filamat\IamSuite;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Contracts\NotificationAdapter;
use Filamat\IamSuite\Contracts\PaymentProviderInterface;
use Filamat\IamSuite\Events\CapabilityRegistered;
use Filamat\IamSuite\Events\SubscriptionChanged;
use Filamat\IamSuite\Models\AccessRequest;
use Filamat\IamSuite\Models\AccessRequestApproval;
use Filamat\IamSuite\Models\ApiKey;
use Filamat\IamSuite\Models\DelegatedAdminScope;
use Filamat\IamSuite\Models\Group;
use Filamat\IamSuite\Models\ImpersonationSession;
use Filamat\IamSuite\Models\PermissionOverride;
use Filamat\IamSuite\Models\PermissionSnapshot;
use Filamat\IamSuite\Models\PermissionTemplate;
use Filamat\IamSuite\Models\PrivilegeActivation;
use Filamat\IamSuite\Models\PrivilegeEligibility;
use Filamat\IamSuite\Models\PrivilegeRequest;
use Filamat\IamSuite\Models\PrivilegeRequestApproval;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\UserInvitation;
use Filamat\IamSuite\Models\UserProfile;
use Filamat\IamSuite\Models\UserSession;
use Filamat\IamSuite\Models\WalletHold;
use Filamat\IamSuite\Models\WalletTransaction;
use Filamat\IamSuite\Models\Webhook;
use Filamat\IamSuite\Observers\AuditableObserver;
use Filamat\IamSuite\Observers\IamUserObserver;
use Filamat\IamSuite\Services\AuditService;
use Filamat\IamSuite\Services\Automation\AutomationRateLimiter;
use Filamat\IamSuite\Services\Automation\IamEventEnvelopeFactory;
use Filamat\IamSuite\Services\Automation\IamEventFactory;
use Filamat\IamSuite\Services\Automation\IamEventPublisher;
use Filamat\IamSuite\Services\Automation\IamWebhookDispatcher;
use Filamat\IamSuite\Services\CapabilityRegistry;
use Filamat\IamSuite\Services\CapabilitySyncService;
use Filamat\IamSuite\Services\ImpersonationService;
use Filamat\IamSuite\Services\InviteUserService;
use Filamat\IamSuite\Services\MfaService;
use Filamat\IamSuite\Services\NotificationAdapterManager;
use Filamat\IamSuite\Services\NotificationService;
use Filamat\IamSuite\Services\PaymentProviderManager;
use Filamat\IamSuite\Services\PrivilegeElevationService;
use Filamat\IamSuite\Services\PrivilegeEligibilityService;
use Filamat\IamSuite\Services\ProtectedActionService;
use Filamat\IamSuite\Services\SecurityEventService;
use Filamat\IamSuite\Services\SessionService;
use Filamat\IamSuite\Services\UserLifecycleService;
use Filamat\IamSuite\Services\WebhookService;
use Filamat\IamSuite\Support\CoreCapabilities;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FilamatIamSuiteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filamat-iam-suite')
            ->hasConfigFile('filamat-iam')
            ->hasConfigFile('n8n_event_catalog')
            ->hasTranslations()
            ->hasViews('filamat-iam')
            ->hasCommands([
                \Filamat\IamSuite\Console\Commands\InstallCommand::class,
                \Filamat\IamSuite\Console\Commands\SyncCapabilitiesCommand::class,
                \Filamat\IamSuite\Console\Commands\IamAiAuditRunCommand::class,
                \Filamat\IamSuite\Console\Commands\IamAutomationPruneCommand::class,
                \Filamat\IamSuite\Console\Commands\IamPamDigestCommand::class,
            ])
            ->hasRoutes(['api', 'web', 'oidc'])
            ->hasMigrations([
                '2025_01_01_000001_create_organizations_table',
                '2025_01_01_000002_create_tenants_table',
                '2025_01_01_000003_create_tenant_user_table',
                '2025_01_01_000004_add_iam_columns_to_users_table',
                '2025_01_01_000005_create_roles_table',
                '2025_01_01_000006_create_permissions_table',
                '2025_01_01_000007_create_model_has_roles_table',
                '2025_01_01_000008_create_model_has_permissions_table',
                '2025_01_01_000009_create_role_has_permissions_table',
                '2025_01_01_000010_create_groups_table',
                '2025_01_01_000011_create_group_user_table',
                '2025_01_01_000012_create_group_role_table',
                '2025_01_01_000013_create_group_permission_table',
                '2025_01_01_000014_create_permission_overrides_table',
                '2025_01_01_000015_create_permission_templates_table',
                '2025_01_01_000016_create_wallets_table',
                '2025_01_01_000017_create_wallet_transactions_table',
                '2025_01_01_000018_create_wallet_holds_table',
                '2025_01_01_000019_create_subscription_plans_table',
                '2025_01_01_000020_create_subscriptions_table',
                '2025_01_01_000021_create_webhooks_table',
                '2025_01_01_000022_create_webhook_deliveries_table',
                '2025_01_01_000023_create_notifications_table',
                '2025_01_01_000024_create_otp_codes_table',
                '2025_01_01_000025_create_audit_logs_table',
                '2025_01_01_000026_create_security_events_table',
                '2025_01_01_000027_create_api_keys_table',
                '2025_01_01_000028_create_access_requests_table',
                '2025_01_01_000029_create_access_request_approvals_table',
                '2025_01_01_000030_create_permission_snapshots_table',
                '2025_01_01_000031_create_delegated_admin_scopes_table',
                '2025_01_01_000032_add_audit_hash_chain_columns',
                '2025_01_01_000033_create_api_key_scopes_table',
                '2025_01_01_000034_create_webhook_nonces_table',
                '2025_01_01_000035_create_user_profiles_table',
                '2025_01_01_000036_add_tenant_id_columns_to_spatie_tables',
                '2025_01_01_000037_create_iam_user_invitations_table',
                '2025_01_01_000038_add_lifecycle_columns_to_tenant_user_table',
                '2025_01_01_000039_create_iam_privilege_eligibilities_table',
                '2025_01_01_000040_create_iam_privilege_requests_table',
                '2025_01_01_000041_create_iam_privilege_request_approvals_table',
                '2025_01_01_000042_create_iam_privilege_activations_table',
                '2025_01_01_000043_create_iam_impersonation_sessions_table',
                '2025_01_01_000044_create_iam_user_sessions_table',
                '2025_01_01_000045_create_iam_protected_action_tokens_table',
                '2025_01_01_000046_create_iam_mfa_methods_table',
                '2025_01_01_000047_add_automation_columns_to_webhooks_table',
                '2025_01_01_000048_create_iam_ai_reports_table',
                '2025_01_01_000049_create_iam_ai_action_proposals_table',
                '2025_01_01_000050_expand_webhook_secret_column',
                '2025_01_01_000051_create_iam_quick_actions_table',
                '2026_02_07_000060_create_iam_oidc_auth_codes_table',
                '2026_02_07_000061_create_iam_oidc_refresh_tokens_table',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(CapabilityRegistryInterface::class, CapabilityRegistry::class);
        $this->app->singleton(CapabilitySyncService::class);
        $this->app->singleton(NotificationAdapterManager::class);
        $this->app->singleton(PaymentProviderManager::class);
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(SecurityEventService::class);
        $this->app->singleton(AuditService::class);
        $this->app->singleton(\Filamat\IamSuite\Services\ModuleCatalog::class);
        $this->app->singleton(\Filamat\IamSuite\Services\RoleTemplateService::class);
        $this->app->singleton(\Filamat\IamSuite\Services\OrganizationEntitlementService::class);
        $this->app->singleton(\Filamat\IamSuite\Services\TenantProvisioningService::class);
        $this->app->singleton(\Filamat\IamSuite\Services\OrganizationProvisioningService::class);
        $this->app->singleton(WebhookService::class);
        $this->app->singleton(ImpersonationService::class);
        $this->app->singleton(AutomationRateLimiter::class);
        $this->app->singleton(IamEventEnvelopeFactory::class);
        $this->app->singleton(IamWebhookDispatcher::class);
        $this->app->singleton(IamEventPublisher::class);
        $this->app->singleton(IamEventFactory::class);
        $this->app->singleton(\Filamat\IamSuite\Services\Automation\IamAiAuditRunner::class);
        $this->app->singleton(\Filamat\IamSuite\Services\Automation\N8nApiClient::class);
        $this->app->singleton(InviteUserService::class);
        $this->app->singleton(UserLifecycleService::class);
        $this->app->singleton(PrivilegeEligibilityService::class);
        $this->app->singleton(PrivilegeElevationService::class);
        $this->app->singleton(SessionService::class);
        $this->app->singleton(ProtectedActionService::class);
        $this->app->singleton(MfaService::class);
        $this->app->singleton(\Filamat\IamSuite\Services\Sso\OidcKeyManager::class);
        $this->app->singleton(\Filamat\IamSuite\Services\Sso\OidcJwtService::class);
        $this->app->singleton(\Filamat\IamSuite\Services\Sso\OidcClientResolver::class);
        $this->app->singleton(\Filamat\IamSuite\Services\Sso\OidcService::class);

        $this->registerCapabilitySyncListeners();

        CoreCapabilities::register($this->app->make(CapabilityRegistryInterface::class));
        if (! $this->app->runningInConsole()) {
            $this->app->make(CapabilitySyncService::class)->markDirty();
        }

        $this->app->bind(NotificationAdapter::class, function () {
            return $this->app->make(NotificationAdapterManager::class)->driver();
        });

        $this->app->bind(PaymentProviderInterface::class, function () {
            return $this->app->make(PaymentProviderManager::class)->driver();
        });
    }

    public function bootingPackage(): void
    {
        $this->app->make(Router::class)->aliasMiddleware('filamat-iam.scope', \Filamat\IamSuite\Http\Middleware\ApiScope::class);
        $this->app->make(Router::class)->aliasMiddleware('filamat-iam.impersonation', \Filamat\IamSuite\Http\Middleware\ImpersonationGuard::class);
        $this->app->make(Router::class)->aliasMiddleware('filamat-iam.session', \Filamat\IamSuite\Http\Middleware\TrackUserSession::class);
        $this->app->make(Router::class)->aliasMiddleware('filamat-iam.mega', \Filamat\IamSuite\Http\Middleware\EnsureMegaSuperAdmin::class);

        Gate::before(function ($user, string $ability) {
            if (method_exists($user, 'hasIamSuiteSuperAdmin') && $user->hasIamSuiteSuperAdmin()) {
                return true;
            }

            return null;
        });

        if (config('filamat-iam.audit.enabled', true)) {
            Group::observe(AuditableObserver::class);
            PermissionOverride::observe(AuditableObserver::class);
            PermissionSnapshot::observe(AuditableObserver::class);
            PermissionTemplate::observe(AuditableObserver::class);
            Subscription::observe(AuditableObserver::class);
            WalletTransaction::observe(AuditableObserver::class);
            WalletHold::observe(AuditableObserver::class);
            Webhook::observe(AuditableObserver::class);
            ApiKey::observe(AuditableObserver::class);
            Role::observe(AuditableObserver::class);
            Permission::observe(AuditableObserver::class);
            AccessRequest::observe(AuditableObserver::class);
            AccessRequestApproval::observe(AuditableObserver::class);
            DelegatedAdminScope::observe(AuditableObserver::class);
            UserInvitation::observe(AuditableObserver::class);
            PrivilegeEligibility::observe(AuditableObserver::class);
            PrivilegeRequest::observe(AuditableObserver::class);
            PrivilegeRequestApproval::observe(AuditableObserver::class);
            PrivilegeActivation::observe(AuditableObserver::class);
            ImpersonationSession::observe(AuditableObserver::class);
            UserSession::observe(AuditableObserver::class);
            UserProfile::observe(AuditableObserver::class);
        }

        $userModel = config('auth.providers.users.model');
        if ($userModel && class_exists($userModel)) {
            $userModel::observe(IamUserObserver::class);
        }
    }

    public function packageBooted(): void
    {
        $this->registerAuthEventListeners();
        if (method_exists($this, 'registerAutomationEventListeners')) {
            $this->registerAutomationEventListeners();
        }
        $this->registerImpersonationBanner();
        if (! $this->app->runningInConsole()) {
            $this->app->make(PrivilegeElevationService::class)->autoExpireIfNeeded();
        }
        $this->registerAutomationSchedules();
    }

    protected function registerAuthEventListeners(): void
    {
        $this->app->make(Dispatcher::class)->listen(Login::class, function (Login $event) {
            if (method_exists($event->user, 'forceFill')) {
                $event->user->forceFill([
                    'last_login_at' => now(),
                    'login_attempts' => 0,
                ])->save();
            }

            $this->app->make(NotificationService::class)->notifyLogin($event->user);
            $this->app->make(SessionService::class)->recordLogin($event->user);
        });

        $this->app->make(Dispatcher::class)->listen(Logout::class, function (Logout $event) {
            $user = $event->user;
            if ($user && method_exists($user, 'forceFill')) {
                $user->forceFill([
                    'last_logout_at' => now(),
                ])->save();
            }

            if ($user) {
                $this->app->make(NotificationService::class)->notifyLogout($user);
                $this->app->make(SessionService::class)->recordLogout($user);
            }
        });

        $this->app->make(Dispatcher::class)->listen(Failed::class, function (Failed $event) {
            $user = $event->user;

            if ($user && method_exists($user, 'forceFill')) {
                $user->forceFill([
                    'login_attempts' => ($user->login_attempts ?? 0) + 1,
                ])->save();
            }

            $this->app->make(SecurityEventService::class)->record('auth.failed', 'warning', $user, null, [
                'identity' => $event->credentials['email'] ?? $event->credentials['phone'] ?? null,
            ]);
        });
    }

    protected function registerCapabilitySyncListeners(): void
    {
        $this->app->make(Dispatcher::class)->listen(CapabilityRegistered::class, function () {
            if ($this->app->runningInConsole()) {
                return;
            }
            $this->app->make(CapabilitySyncService::class)->markDirty();
        });

        $this->app->booted(function () {
            if ($this->app->runningInConsole()) {
                return;
            }
            $this->app->make(CapabilitySyncService::class)->autoSyncIfNeeded();
        });
    }

    protected function registerAutomationEventListeners(): void
    {
        $this->app->make(Dispatcher::class)->listen(SubscriptionChanged::class, function (SubscriptionChanged $event) {
            $factory = $this->app->make(IamEventFactory::class);
            $publisher = $this->app->make(IamEventPublisher::class);

            $automationEvent = $factory->fromSubscription($event->subscription);
            if ($automationEvent) {
                $publisher->publish($automationEvent);
            }
        });
    }

    protected function registerAutomationSchedules(): void
    {
        if (! (bool) config('filamat-iam.automation.schedule.enabled', true)) {
            return;
        }

        $this->app->booted(function () {
            if (! (bool) config('filamat-iam.automation.enabled', true)) {
                return;
            }

            $schedule = app(Schedule::class);
            $auditTime = (string) config('filamat-iam.automation.schedule.audit_time', '02:00');
            $pruneTime = (string) config('filamat-iam.automation.schedule.prune_time', '03:00');

            $schedule->command('iam:ai-audit:run')->dailyAt($auditTime);
            $schedule->command('iam:automation:prune')->dailyAt($pruneTime);
        });
    }

    protected function registerImpersonationBanner(): void
    {
        if (! class_exists(FilamentView::class)) {
            return;
        }

        FilamentView::registerRenderHook(PanelsRenderHook::BODY_START, function () {
            $auth = Filament::auth();
            if (! $auth || ! $auth->check()) {
                return '';
            }

            if (! app(ImpersonationService::class)->isImpersonating()) {
                return '';
            }

            return view('filamat-iam::partials.impersonation-banner', [
                'session' => app(ImpersonationService::class)->currentSession(),
            ]);
        });
    }
}
