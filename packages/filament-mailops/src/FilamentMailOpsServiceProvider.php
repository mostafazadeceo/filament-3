<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentMailOps\Models\MailAlias;
use Haida\FilamentMailOps\Models\MailDomain;
use Haida\FilamentMailOps\Models\MailInboundMessage;
use Haida\FilamentMailOps\Models\MailMailbox;
use Haida\FilamentMailOps\Models\MailOutboundMessage;
use Haida\FilamentMailOps\Policies\MailAliasPolicy;
use Haida\FilamentMailOps\Policies\MailDomainPolicy;
use Haida\FilamentMailOps\Policies\MailInboundMessagePolicy;
use Haida\FilamentMailOps\Policies\MailMailboxPolicy;
use Haida\FilamentMailOps\Policies\MailOutboundMessagePolicy;
use Haida\FilamentMailOps\Services\DomainDnsAuditService;
use Haida\FilamentMailOps\Services\ImapInboxReader;
use Haida\FilamentMailOps\Services\MailSender;
use Haida\FilamentMailOps\Services\MailuClient;
use Haida\FilamentMailOps\Services\MailuSyncService;
use Haida\FilamentMailOps\Support\MailOpsCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMailOpsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-mailops')
            ->hasConfigFile('filament-mailops')
            ->hasTranslations()
            ->hasRoutes('api')
            ->hasMigrations([
                '2026_02_10_000001_create_mailops_domains_table',
                '2026_02_10_000002_create_mailops_mailboxes_table',
                '2026_02_10_000003_create_mailops_aliases_table',
                '2026_02_10_000004_create_mailops_outbound_messages_table',
                '2026_02_10_000005_create_mailops_inbound_messages_table',
                '2026_02_20_000006_add_dns_audit_columns_to_mailops_domains_table',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(MailuClient::class);
        $this->app->singleton(DomainDnsAuditService::class);
        $this->app->singleton(MailuSyncService::class);
        $this->app->singleton(MailSender::class);
        $this->app->singleton(ImapInboxReader::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(MailDomain::class, MailDomainPolicy::class);
        Gate::policy(MailMailbox::class, MailMailboxPolicy::class);
        Gate::policy(MailAlias::class, MailAliasPolicy::class);
        Gate::policy(MailOutboundMessage::class, MailOutboundMessagePolicy::class);
        Gate::policy(MailInboundMessage::class, MailInboundMessagePolicy::class);

        if (interface_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            MailOpsCapabilities::register($registry);
        }
    }
}
