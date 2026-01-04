<?php

namespace Haida\FilamentLoyaltyClub;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentLoyaltyClub\Filament\Pages\LoyaltySettingsPage;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyAuditEventResource;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyBadgeResource;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyCampaignResource;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyCouponResource;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyCustomerResource;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyFraudSignalResource;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyMissionResource;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyPointsRuleResource;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyReferralProgramResource;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyReferralResource;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyRewardResource;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltySegmentResource;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyTierResource;

class FilamentLoyaltyClubPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-loyalty-club';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            LoyaltyCustomerResource::class,
            LoyaltyTierResource::class,
            LoyaltyPointsRuleResource::class,
            LoyaltyRewardResource::class,
            LoyaltyCouponResource::class,
            LoyaltyReferralProgramResource::class,
            LoyaltyReferralResource::class,
            LoyaltyMissionResource::class,
            LoyaltyBadgeResource::class,
            LoyaltySegmentResource::class,
            LoyaltyCampaignResource::class,
            LoyaltyFraudSignalResource::class,
            LoyaltyAuditEventResource::class,
        ]);

        $panel->pages([
            LoyaltySettingsPage::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
