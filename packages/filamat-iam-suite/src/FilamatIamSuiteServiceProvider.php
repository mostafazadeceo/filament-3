<?php

declare(strict_types=1);

namespace Filamat\IamSuite;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Contracts\NotificationAdapter;
use Filamat\IamSuite\Contracts\PaymentProviderInterface;
use Filamat\IamSuite\Events\CapabilityRegistered;
use Filamat\IamSuite\Models\AccessRequest;
use Filamat\IamSuite\Models\AccessRequestApproval;
use Filamat\IamSuite\Models\ApiKey;
use Filamat\IamSuite\Models\DelegatedAdminScope;
use Filamat\IamSuite\Models\Group;
use Filamat\IamSuite\Models\PermissionOverride;
use Filamat\IamSuite\Models\PermissionSnapshot;
use Filamat\IamSuite\Models\PermissionTemplate;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\UserProfile;
use Filamat\IamSuite\Models\WalletHold;
use Filamat\IamSuite\Models\WalletTransaction;
use Filamat\IamSuite\Models\Webhook;
use Filamat\IamSuite\Observers\AuditableObserver;
use Filamat\IamSuite\Services\AuditService;
use Filamat\IamSuite\Services\CapabilityRegistry;
use Filamat\IamSuite\Services\CapabilitySyncService;
use Filamat\IamSuite\Services\ImpersonationService;
use Filamat\IamSuite\Services\NotificationAdapterManager;
use Filamat\IamSuite\Services\NotificationService;
use Filamat\IamSuite\Services\PaymentProviderManager;
use Filamat\IamSuite\Services\SecurityEventService;
use Filamat\IamSuite\Services\WebhookService;
use Filamat\IamSuite\Support\CoreCapabilities;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
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
            ->hasTranslations()
            ->hasViews('filamat-iam')
            ->hasCommands([
                \Filamat\IamSuite\Console\Commands\InstallCommand::class,
                \Filamat\IamSuite\Console\Commands\SyncCapabilitiesCommand::class,
            ])
            ->hasRoutes(['api', 'web'])
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
        $this->app->singleton(WebhookService::class);
        $this->app->singleton(ImpersonationService::class);

        $this->registerCapabilitySyncListeners();

        CoreCapabilities::register($this->app->make(CapabilityRegistryInterface::class));
        $this->app->make(CapabilitySyncService::class)->markDirty();

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
            UserProfile::observe(AuditableObserver::class);
        }
    }

    public function packageBooted(): void
    {
        $this->registerAuthEventListeners();
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
        });

        $this->app->make(Dispatcher::class)->listen(Logout::class, function (Logout $event) {
            if (method_exists($event->user, 'forceFill')) {
                $event->user->forceFill([
                    'last_logout_at' => now(),
                ])->save();
            }

            $this->app->make(NotificationService::class)->notifyLogout($event->user);
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
            $this->app->make(CapabilitySyncService::class)->markDirty();
        });

        $this->app->booted(function () {
            $this->app->make(CapabilitySyncService::class)->autoSyncIfNeeded();
        });
    }
}
