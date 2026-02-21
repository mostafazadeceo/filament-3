<?php

namespace Haida\FilamentLoyaltyClub;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentLoyaltyClub\Adapters\CompositePurchaseAdapter;
use Haida\FilamentLoyaltyClub\Adapters\IamWalletAdapter;
use Haida\FilamentLoyaltyClub\Adapters\InternalWalletAdapter;
use Haida\FilamentLoyaltyClub\Console\Commands\LoyaltyRetentionPruneCommand;
use Haida\FilamentLoyaltyClub\Contracts\AiProviderInterface;
use Haida\FilamentLoyaltyClub\Contracts\PurchaseAdapterInterface;
use Haida\FilamentLoyaltyClub\Contracts\WalletAdapterInterface;
use Haida\FilamentLoyaltyClub\Models\LoyaltyAuditEvent;
use Haida\FilamentLoyaltyClub\Models\LoyaltyBadge;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCampaign;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCoupon;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyEvent;
use Haida\FilamentLoyaltyClub\Models\LoyaltyFraudSignal;
use Haida\FilamentLoyaltyClub\Models\LoyaltyMission;
use Haida\FilamentLoyaltyClub\Models\LoyaltyPointsRule;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReferral;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReferralProgram;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReward;
use Haida\FilamentLoyaltyClub\Models\LoyaltySegment;
use Haida\FilamentLoyaltyClub\Models\LoyaltyTier;
use Haida\FilamentLoyaltyClub\Policies\LoyaltyAuditEventPolicy;
use Haida\FilamentLoyaltyClub\Policies\LoyaltyBadgePolicy;
use Haida\FilamentLoyaltyClub\Policies\LoyaltyCampaignPolicy;
use Haida\FilamentLoyaltyClub\Policies\LoyaltyCouponPolicy;
use Haida\FilamentLoyaltyClub\Policies\LoyaltyCustomerPolicy;
use Haida\FilamentLoyaltyClub\Policies\LoyaltyEventPolicy;
use Haida\FilamentLoyaltyClub\Policies\LoyaltyFraudSignalPolicy;
use Haida\FilamentLoyaltyClub\Policies\LoyaltyMissionPolicy;
use Haida\FilamentLoyaltyClub\Policies\LoyaltyPointsRulePolicy;
use Haida\FilamentLoyaltyClub\Policies\LoyaltyReferralPolicy;
use Haida\FilamentLoyaltyClub\Policies\LoyaltyReferralProgramPolicy;
use Haida\FilamentLoyaltyClub\Policies\LoyaltyRewardPolicy;
use Haida\FilamentLoyaltyClub\Policies\LoyaltySegmentPolicy;
use Haida\FilamentLoyaltyClub\Policies\LoyaltyTierPolicy;
use Haida\FilamentLoyaltyClub\Services\Ai\FakeAiProvider;
use Haida\FilamentLoyaltyClub\Services\LoyaltyRuleEngine;
use Haida\FilamentLoyaltyClub\Support\LoyaltyCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentLoyaltyClubServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-loyalty-club')
            ->hasConfigFile('filament-loyalty-club')
            ->hasViews()
            ->hasTranslations()
            ->hasRoutes('api')
            ->hasCommands([
                LoyaltyRetentionPruneCommand::class,
            ])
            ->hasMigrations([
                '2026_01_05_000001_create_loyalty_core_tables',
                '2026_01_05_000002_create_loyalty_wallet_tables',
                '2026_01_05_000003_create_loyalty_reward_tables',
                '2026_01_05_000004_create_loyalty_referral_tables',
                '2026_01_05_000005_create_loyalty_gamification_tables',
                '2026_01_05_000006_create_loyalty_segment_campaign_tables',
                '2026_01_05_000007_create_loyalty_audit_fraud_tables',
                '2026_01_05_000008_create_loyalty_metrics_tables',
                '2026_01_05_000009_create_loyalty_donation_tables',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(LoyaltyRuleEngine::class);

        $this->app->bind(WalletAdapterInterface::class, function ($app) {
            $adapter = (string) config('filament-loyalty-club.features.cashback.adapter', 'internal');
            if ($adapter === 'iam' && class_exists(IamWalletAdapter::class)) {
                return $app->make(IamWalletAdapter::class);
            }

            return $app->make(InternalWalletAdapter::class);
        });

        $this->app->bind(PurchaseAdapterInterface::class, function ($app) {
            return $app->make(CompositePurchaseAdapter::class);
        });

        $this->app->bind(AiProviderInterface::class, function () {
            return new FakeAiProvider;
        });
    }

    public function packageBooted(): void
    {
        Gate::policy(LoyaltyCustomer::class, LoyaltyCustomerPolicy::class);
        Gate::policy(LoyaltyTier::class, LoyaltyTierPolicy::class);
        Gate::policy(LoyaltyPointsRule::class, LoyaltyPointsRulePolicy::class);
        Gate::policy(LoyaltyReward::class, LoyaltyRewardPolicy::class);
        Gate::policy(LoyaltyCoupon::class, LoyaltyCouponPolicy::class);
        Gate::policy(LoyaltyReferralProgram::class, LoyaltyReferralProgramPolicy::class);
        Gate::policy(LoyaltyReferral::class, LoyaltyReferralPolicy::class);
        Gate::policy(LoyaltyMission::class, LoyaltyMissionPolicy::class);
        Gate::policy(LoyaltyBadge::class, LoyaltyBadgePolicy::class);
        Gate::policy(LoyaltySegment::class, LoyaltySegmentPolicy::class);
        Gate::policy(LoyaltyCampaign::class, LoyaltyCampaignPolicy::class);
        Gate::policy(LoyaltyFraudSignal::class, LoyaltyFraudSignalPolicy::class);
        Gate::policy(LoyaltyAuditEvent::class, LoyaltyAuditEventPolicy::class);
        Gate::policy(LoyaltyEvent::class, LoyaltyEventPolicy::class);

        if (interface_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            LoyaltyCapabilities::register($registry);
        }

        Gate::define('loyalty.view', fn () => IamAuthorization::allows('loyalty.view'));
    }
}
