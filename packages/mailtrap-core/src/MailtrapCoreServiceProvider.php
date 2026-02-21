<?php

declare(strict_types=1);

namespace Haida\MailtrapCore;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\CommerceOrders\Events\OrderPaid;
use Haida\MailtrapCore\Listeners\GrantMailtrapEntitlements;
use Haida\MailtrapCore\Models\MailtrapAudience;
use Haida\MailtrapCore\Models\MailtrapCampaign;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapInbox;
use Haida\MailtrapCore\Models\MailtrapMessage;
use Haida\MailtrapCore\Models\MailtrapOffer;
use Haida\MailtrapCore\Models\MailtrapSendingDomain;
use Haida\MailtrapCore\Models\MailtrapSingleSend;
use Haida\MailtrapCore\Policies\MailtrapAudiencePolicy;
use Haida\MailtrapCore\Policies\MailtrapCampaignPolicy;
use Haida\MailtrapCore\Policies\MailtrapConnectionPolicy;
use Haida\MailtrapCore\Policies\MailtrapInboxPolicy;
use Haida\MailtrapCore\Policies\MailtrapMessagePolicy;
use Haida\MailtrapCore\Policies\MailtrapOfferPolicy;
use Haida\MailtrapCore\Policies\MailtrapSendingDomainPolicy;
use Haida\MailtrapCore\Policies\MailtrapSingleSendPolicy;
use Haida\MailtrapCore\Services\MailtrapAudienceService;
use Haida\MailtrapCore\Services\MailtrapCampaignService;
use Haida\MailtrapCore\Services\MailtrapConnectionService;
use Haida\MailtrapCore\Services\MailtrapDomainService;
use Haida\MailtrapCore\Services\MailtrapInboxService;
use Haida\MailtrapCore\Services\MailtrapMessageService;
use Haida\MailtrapCore\Services\MailtrapOfferService;
use Haida\MailtrapCore\Services\MailtrapSendService;
use Haida\MailtrapCore\Services\MailtrapSingleSendService;
use Haida\MailtrapCore\Support\MailtrapCapabilities;
use Haida\MailtrapCore\Support\MailtrapRateLimiter;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MailtrapCoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('mailtrap-core')
            ->hasConfigFile('mailtrap-core')
            ->hasTranslations()
            ->hasRoutes('api')
            ->hasMigrations([
                '2026_01_02_000001_create_mailtrap_connections_table',
                '2026_01_02_000002_create_mailtrap_inboxes_table',
                '2026_01_02_000003_create_mailtrap_messages_table',
                '2026_01_02_000004_create_mailtrap_sending_domains_table',
                '2026_01_02_000005_create_mailtrap_offers_table',
                '2026_01_02_000006_add_send_api_token_to_mailtrap_connections_table',
                '2026_01_02_000007_create_mailtrap_audiences_table',
                '2026_01_02_000008_create_mailtrap_audience_contacts_table',
                '2026_01_02_000009_create_mailtrap_campaigns_table',
                '2026_01_02_000010_create_mailtrap_campaign_sends_table',
                '2026_01_02_000011_create_mailtrap_single_sends_table',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(MailtrapRateLimiter::class);
        $this->app->singleton(MailtrapConnectionService::class);
        $this->app->singleton(MailtrapInboxService::class);
        $this->app->singleton(MailtrapMessageService::class);
        $this->app->singleton(MailtrapDomainService::class);
        $this->app->singleton(MailtrapOfferService::class);
        $this->app->singleton(MailtrapSendService::class);
        $this->app->singleton(MailtrapSingleSendService::class);
        $this->app->singleton(MailtrapAudienceService::class);
        $this->app->singleton(MailtrapCampaignService::class);

        if (interface_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            MailtrapCapabilities::register($registry);
        }
    }

    public function packageBooted(): void
    {
        Gate::policy(MailtrapConnection::class, MailtrapConnectionPolicy::class);
        Gate::policy(MailtrapInbox::class, MailtrapInboxPolicy::class);
        Gate::policy(MailtrapMessage::class, MailtrapMessagePolicy::class);
        Gate::policy(MailtrapSendingDomain::class, MailtrapSendingDomainPolicy::class);
        Gate::policy(MailtrapOffer::class, MailtrapOfferPolicy::class);
        Gate::policy(MailtrapAudience::class, MailtrapAudiencePolicy::class);
        Gate::policy(MailtrapCampaign::class, MailtrapCampaignPolicy::class);
        Gate::policy(MailtrapSingleSend::class, MailtrapSingleSendPolicy::class);

        Event::listen(OrderPaid::class, GrantMailtrapEntitlements::class);
    }
}
