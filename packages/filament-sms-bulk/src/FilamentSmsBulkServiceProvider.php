<?php

declare(strict_types=1);

namespace Haida\SmsBulk;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\SmsBulk\Models\SmsBulkAuditLog;
use Haida\SmsBulk\Models\SmsBulkCampaign;
use Haida\SmsBulk\Models\SmsBulkCampaignRecipient;
use Haida\SmsBulk\Models\SmsBulkConsentRegistry;
use Haida\SmsBulk\Models\SmsBulkContact;
use Haida\SmsBulk\Models\SmsBulkDraftGroup;
use Haida\SmsBulk\Models\SmsBulkDraftMessage;
use Haida\SmsBulk\Models\SmsBulkImportJob;
use Haida\SmsBulk\Models\SmsBulkPatternTemplate;
use Haida\SmsBulk\Models\SmsBulkPhonebook;
use Haida\SmsBulk\Models\SmsBulkPhonebookOption;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Models\SmsBulkQuietHoursProfile;
use Haida\SmsBulk\Models\SmsBulkQuotaPolicy;
use Haida\SmsBulk\Models\SmsBulkRateLimitPolicy;
use Haida\SmsBulk\Models\SmsBulkRoutingPolicy;
use Haida\SmsBulk\Models\SmsBulkSenderIdentity;
use Haida\SmsBulk\Models\SmsBulkSuppressionList;
use Haida\SmsBulk\Models\SmsBulkWebhookLog;
use Haida\SmsBulk\Policies\SmsBulkModelPolicy;
use Haida\SmsBulk\Services\ProviderClientFactory;
use Haida\SmsBulk\Support\SmsBulkCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentSmsBulkServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-sms-bulk')
            ->hasConfigFile('filament-sms-bulk')
            ->hasTranslations()
            ->hasViews()
            ->hasRoutes('api')
            ->hasMigrations([
                '2026_02_20_170000_create_sms_bulk_provider_connections_table',
                '2026_02_20_170001_create_sms_bulk_sender_identities_table',
                '2026_02_20_170002_create_sms_bulk_suppression_lists_table',
                '2026_02_20_170003_create_sms_bulk_consent_registries_table',
                '2026_02_20_170004_create_sms_bulk_phonebooks_table',
                '2026_02_20_170005_create_sms_bulk_phonebook_options_table',
                '2026_02_20_170006_create_sms_bulk_contacts_table',
                '2026_02_20_170007_create_sms_bulk_draft_groups_table',
                '2026_02_20_170008_create_sms_bulk_draft_messages_table',
                '2026_02_20_170009_create_sms_bulk_pattern_templates_table',
                '2026_02_20_170010_create_sms_bulk_quiet_hours_profiles_table',
                '2026_02_20_170011_create_sms_bulk_quota_policies_table',
                '2026_02_20_170012_create_sms_bulk_rate_limit_policies_table',
                '2026_02_20_170013_create_sms_bulk_routing_policies_table',
                '2026_02_20_170014_create_sms_bulk_campaigns_table',
                '2026_02_20_170015_create_sms_bulk_campaign_recipients_table',
                '2026_02_20_170016_create_sms_bulk_import_jobs_table',
                '2026_02_20_170017_create_sms_bulk_webhook_logs_table',
                '2026_02_20_170018_create_sms_bulk_audit_logs_table',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ProviderClientFactory::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(SmsBulkProviderConnection::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkSenderIdentity::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkSuppressionList::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkConsentRegistry::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkPhonebook::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkPhonebookOption::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkContact::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkDraftGroup::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkDraftMessage::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkPatternTemplate::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkQuietHoursProfile::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkQuotaPolicy::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkRateLimitPolicy::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkRoutingPolicy::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkCampaign::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkCampaignRecipient::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkImportJob::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkWebhookLog::class, SmsBulkModelPolicy::class);
        Gate::policy(SmsBulkAuditLog::class, SmsBulkModelPolicy::class);

        $registry = $this->app->make(CapabilityRegistryInterface::class);
        SmsBulkCapabilities::register($registry);
    }
}
