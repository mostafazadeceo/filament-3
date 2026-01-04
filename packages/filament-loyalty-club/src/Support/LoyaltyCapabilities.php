<?php

namespace Haida\FilamentLoyaltyClub\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
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

final class LoyaltyCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-loyalty-club',
            self::permissions(),
            [
                'loyalty' => true,
            ],
            [],
            [
                LoyaltyCustomerPolicy::class,
                LoyaltyTierPolicy::class,
                LoyaltyPointsRulePolicy::class,
                LoyaltyRewardPolicy::class,
                LoyaltyCouponPolicy::class,
                LoyaltyReferralProgramPolicy::class,
                LoyaltyReferralPolicy::class,
                LoyaltyMissionPolicy::class,
                LoyaltyBadgePolicy::class,
                LoyaltySegmentPolicy::class,
                LoyaltyCampaignPolicy::class,
                LoyaltyFraudSignalPolicy::class,
                LoyaltyAuditEventPolicy::class,
                LoyaltyEventPolicy::class,
            ],
            [
                'loyalty' => 'باشگاه مشتریان',
                'loyalty_customers' => 'مشتریان',
                'loyalty_rules' => 'قوانین امتیاز',
                'loyalty_rewards' => 'پاداش‌ها',
                'loyalty_referrals' => 'دعوت و معرفی',
                'loyalty_gamification' => 'بازی‌سازی',
                'loyalty_segments' => 'بخش‌بندی',
                'loyalty_campaigns' => 'کمپین‌ها',
                'loyalty_controls' => 'کنترل و ممیزی',
                'loyalty_settings' => 'تنظیمات',
            ]
        );

        self::$registered = true;
    }

    /**
     * @return array<int, string>
     */
    public static function permissions(): array
    {
        return [
            'loyalty.view',
            'loyalty.customer.view',
            'loyalty.customer.manage',
            'loyalty.tier.view',
            'loyalty.tier.manage',
            'loyalty.rule.view',
            'loyalty.rule.manage',
            'loyalty.reward.view',
            'loyalty.reward.manage',
            'loyalty.reward.redeem',
            'loyalty.coupon.view',
            'loyalty.coupon.manage',
            'loyalty.coupon.issue',
            'loyalty.coupon.redeem',
            'loyalty.referral.view',
            'loyalty.referral.manage',
            'loyalty.referral.program.view',
            'loyalty.referral.program.manage',
            'loyalty.mission.view',
            'loyalty.mission.manage',
            'loyalty.badge.view',
            'loyalty.badge.manage',
            'loyalty.segment.view',
            'loyalty.segment.manage',
            'loyalty.campaign.view',
            'loyalty.campaign.manage',
            'loyalty.campaign.send',
            'loyalty.fraud.view',
            'loyalty.fraud.manage',
            'loyalty.audit.view',
            'loyalty.event.ingest',
            'loyalty.settings.manage',
            'loyalty.ai.use',
            'loyalty.ai.manage',
        ];
    }
}
