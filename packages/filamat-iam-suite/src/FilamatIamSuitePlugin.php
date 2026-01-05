<?php

declare(strict_types=1);

namespace Filamat\IamSuite;

use Filamat\IamSuite\Filament\Pages\MegaAdminGuide;
use Filamat\IamSuite\Filament\Pages\MegaAdminOnboarding;
use Filamat\IamSuite\Filament\Pages\NotificationDashboard;
use Filamat\IamSuite\Filament\Pages\OrganizationGuide;
use Filamat\IamSuite\Filament\Pages\PermissionSimulator;
use Filamat\IamSuite\Filament\Pages\SuperAdminDashboard;
use Filamat\IamSuite\Filament\Pages\TenantDashboard;
use Filamat\IamSuite\Filament\Pages\TenantSettings;
use Filamat\IamSuite\Filament\Resources\ApiKeyResource;
use Filamat\IamSuite\Filament\Resources\AuditLogResource;
use Filamat\IamSuite\Filament\Resources\GroupResource;
use Filamat\IamSuite\Filament\Resources\IamAiActionProposalResource;
use Filamat\IamSuite\Filament\Resources\IamAiReportResource;
use Filamat\IamSuite\Filament\Resources\ImpersonationSessionResource;
use Filamat\IamSuite\Filament\Resources\MfaMethodResource;
use Filamat\IamSuite\Filament\Resources\NotificationResource;
use Filamat\IamSuite\Filament\Resources\OrganizationResource;
use Filamat\IamSuite\Filament\Resources\OrganizationWorkspaceResource;
use Filamat\IamSuite\Filament\Resources\PermissionOverrideResource;
use Filamat\IamSuite\Filament\Resources\PermissionResource;
use Filamat\IamSuite\Filament\Resources\PermissionTemplateResource;
use Filamat\IamSuite\Filament\Resources\PrivilegeActivationResource;
use Filamat\IamSuite\Filament\Resources\PrivilegeEligibilityResource;
use Filamat\IamSuite\Filament\Resources\PrivilegeRequestResource;
use Filamat\IamSuite\Filament\Resources\QuickActionResource;
use Filamat\IamSuite\Filament\Resources\RoleResource;
use Filamat\IamSuite\Filament\Resources\SecurityEventResource;
use Filamat\IamSuite\Filament\Resources\SubscriptionPlanResource;
use Filamat\IamSuite\Filament\Resources\SubscriptionResource;
use Filamat\IamSuite\Filament\Resources\TenantResource;
use Filamat\IamSuite\Filament\Resources\UserInvitationResource;
use Filamat\IamSuite\Filament\Resources\UserResource;
use Filamat\IamSuite\Filament\Resources\UserSessionResource;
use Filamat\IamSuite\Filament\Resources\WalletHoldResource;
use Filamat\IamSuite\Filament\Resources\WalletResource;
use Filamat\IamSuite\Filament\Resources\WalletTransactionResource;
use Filamat\IamSuite\Filament\Resources\WebhookResource;
use Filamat\IamSuite\Filament\Widgets\AutomationInsightStatsWidget;
use Filamat\IamSuite\Filament\Widgets\MegaAdminDeliveryWidget;
use Filamat\IamSuite\Filament\Widgets\MegaAdminOperationsWidget;
use Filamat\IamSuite\Filament\Widgets\MegaAdminOverviewWidget;
use Filamat\IamSuite\Filament\Widgets\NotificationChannelBreakdownWidget;
use Filamat\IamSuite\Filament\Widgets\NotificationDeliveryChartWidget;
use Filamat\IamSuite\Filament\Widgets\NotificationFailuresWidget;
use Filamat\IamSuite\Filament\Widgets\NotificationStatsWidget;
use Filamat\IamSuite\Filament\Widgets\NotificationStatusTimelineWidget;
use Filamat\IamSuite\Filament\Widgets\QuickActionsWidget;
use Filamat\IamSuite\Filament\Widgets\RecentAuditLogsWidget;
use Filamat\IamSuite\Filament\Widgets\RecentNotificationsWidget;
use Filamat\IamSuite\Filament\Widgets\RecentSecurityEventsWidget;
use Filamat\IamSuite\Filament\Widgets\SuperAdminStatsWidget;
use Filamat\IamSuite\Filament\Widgets\TenantStatsWidget;
use Filamat\IamSuite\Filament\Widgets\WalletVolumeChartWidget;
use Filamat\IamSuite\Filament\Widgets\WebhookHealthChartWidget;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;

class FilamatIamSuitePlugin implements Plugin
{
    protected array $superAdminPanels = [];

    protected array $tenantPanels = [];

    public static function make(): static
    {
        return new static;
    }

    public function getId(): string
    {
        return 'filamat-iam-suite';
    }

    public function superAdminPanels(array $panels): static
    {
        $this->superAdminPanels = $panels;

        return $this;
    }

    public function tenantPanels(array $panels): static
    {
        $this->tenantPanels = $panels;

        return $this;
    }

    public function register(Panel $panel): void
    {
        $panelId = $panel->getId();

        if ($this->isSuperAdminPanel($panelId)) {
            $panel
                ->middleware([
                    'filamat-iam.mega',
                    'filamat-iam.impersonation',
                    'filamat-iam.session',
                ])
                ->renderHook(PanelsRenderHook::GLOBAL_SEARCH_AFTER, fn () => view('filamat-iam::components.topbar-account'))
                ->pages([
                    SuperAdminDashboard::class,
                    MegaAdminOnboarding::class,
                    MegaAdminGuide::class,
                    NotificationDashboard::class,
                    PermissionSimulator::class,
                ])
                ->widgets([
                    MegaAdminOverviewWidget::class,
                    MegaAdminOperationsWidget::class,
                    MegaAdminDeliveryWidget::class,
                    SuperAdminStatsWidget::class,
                    NotificationStatsWidget::class,
                    QuickActionsWidget::class,
                    WalletVolumeChartWidget::class,
                    NotificationDeliveryChartWidget::class,
                    NotificationStatusTimelineWidget::class,
                    NotificationChannelBreakdownWidget::class,
                    NotificationFailuresWidget::class,
                    WebhookHealthChartWidget::class,
                    AutomationInsightStatsWidget::class,
                    RecentSecurityEventsWidget::class,
                    RecentAuditLogsWidget::class,
                    RecentNotificationsWidget::class,
                ])
                ->resources([
                    OrganizationResource::class,
                    TenantResource::class,
                    UserResource::class,
                    RoleResource::class,
                    PermissionResource::class,
                    GroupResource::class,
                    PermissionTemplateResource::class,
                    PermissionOverrideResource::class,
                    UserInvitationResource::class,
                    PrivilegeEligibilityResource::class,
                    PrivilegeRequestResource::class,
                    PrivilegeActivationResource::class,
                    ImpersonationSessionResource::class,
                    UserSessionResource::class,
                    MfaMethodResource::class,
                    WalletResource::class,
                    WalletTransactionResource::class,
                    WalletHoldResource::class,
                    SubscriptionPlanResource::class,
                    SubscriptionResource::class,
                    WebhookResource::class,
                    ApiKeyResource::class,
                    NotificationResource::class,
                    SecurityEventResource::class,
                    AuditLogResource::class,
                    IamAiReportResource::class,
                    IamAiActionProposalResource::class,
                    QuickActionResource::class,
                ]);

            return;
        }

        if ($this->isTenantPanel($panelId)) {
            $panel
                ->middleware([
                    'filamat-iam.impersonation',
                    'filamat-iam.session',
                ])
                ->renderHook(PanelsRenderHook::GLOBAL_SEARCH_AFTER, fn () => view('filamat-iam::components.topbar-account'))
                ->pages([
                    TenantDashboard::class,
                    OrganizationGuide::class,
                    NotificationDashboard::class,
                    PermissionSimulator::class,
                    TenantSettings::class,
                ])
                ->widgets([
                    TenantStatsWidget::class,
                    NotificationStatsWidget::class,
                    QuickActionsWidget::class,
                    WalletVolumeChartWidget::class,
                    NotificationDeliveryChartWidget::class,
                    NotificationStatusTimelineWidget::class,
                    NotificationChannelBreakdownWidget::class,
                    NotificationFailuresWidget::class,
                    WebhookHealthChartWidget::class,
                    AutomationInsightStatsWidget::class,
                    RecentSecurityEventsWidget::class,
                    RecentAuditLogsWidget::class,
                    RecentNotificationsWidget::class,
                ])
                ->resources([
                    UserResource::class,
                    OrganizationWorkspaceResource::class,
                    RoleResource::class,
                    PermissionResource::class,
                    GroupResource::class,
                    PermissionTemplateResource::class,
                    PermissionOverrideResource::class,
                    UserInvitationResource::class,
                    PrivilegeEligibilityResource::class,
                    PrivilegeRequestResource::class,
                    PrivilegeActivationResource::class,
                    ImpersonationSessionResource::class,
                    UserSessionResource::class,
                    MfaMethodResource::class,
                    WalletResource::class,
                    WalletTransactionResource::class,
                    WalletHoldResource::class,
                    SubscriptionPlanResource::class,
                    SubscriptionResource::class,
                    WebhookResource::class,
                    ApiKeyResource::class,
                    NotificationResource::class,
                    SecurityEventResource::class,
                    AuditLogResource::class,
                    IamAiReportResource::class,
                    IamAiActionProposalResource::class,
                    QuickActionResource::class,
                ]);
        }
    }

    public function boot(Panel $panel): void
    {
        // Reserved for future panel hooks.
    }

    protected function isSuperAdminPanel(?string $panelId): bool
    {
        $list = $this->superAdminPanels;
        if ($list === []) {
            $list = (array) config('filamat-iam.super_admin_panels', ['admin']);
        }

        return $panelId !== null && in_array($panelId, $list, true);
    }

    protected function isTenantPanel(?string $panelId): bool
    {
        if ($panelId === null) {
            return false;
        }

        if ($this->tenantPanels !== []) {
            return in_array($panelId, $this->tenantPanels, true);
        }

        return ! $this->isSuperAdminPanel($panelId);
    }
}
