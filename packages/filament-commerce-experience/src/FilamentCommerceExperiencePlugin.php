<?php

namespace Haida\FilamentCommerceExperience;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceBuyNowPreferenceResource;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceCsatSurveyResource;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceQuestionResource;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceReviewResource;

class FilamentCommerceExperiencePlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-commerce-experience';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ExperienceReviewResource::class,
            ExperienceQuestionResource::class,
            ExperienceCsatSurveyResource::class,
            ExperienceBuyNowPreferenceResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
