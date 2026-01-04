<?php

namespace Haida\FilamentCommerceExperience\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentCommerceExperience\Policies\ExperienceBuyNowPreferencePolicy;
use Haida\FilamentCommerceExperience\Policies\ExperienceCsatSurveyPolicy;
use Haida\FilamentCommerceExperience\Policies\ExperienceQuestionPolicy;
use Haida\FilamentCommerceExperience\Policies\ExperienceReviewPolicy;

final class ExperienceCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-commerce-experience',
            self::permissions(),
            [
                'experience' => true,
            ],
            [],
            [
                ExperienceReviewPolicy::class,
                ExperienceQuestionPolicy::class,
                ExperienceCsatSurveyPolicy::class,
                ExperienceBuyNowPreferencePolicy::class,
            ],
            [
                'experience' => 'تجربه مشتری',
                'experience_reviews' => 'نظرات و پرسش‌ها',
                'experience_csat' => 'CSAT/NPS',
                'experience_buy_now' => 'خرید فوری',
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
            'experience.reviews.view',
            'experience.reviews.moderate',
            'experience.csat.view',
            'experience.csat.manage',
            'experience.buy_now.manage',
        ];
    }
}
