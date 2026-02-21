<?php

namespace Haida\FilamentCommerceExperience;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentCommerceExperience\Models\ExperienceBuyNowPreference;
use Haida\FilamentCommerceExperience\Models\ExperienceCsatSurvey;
use Haida\FilamentCommerceExperience\Models\ExperienceQuestion;
use Haida\FilamentCommerceExperience\Models\ExperienceReview;
use Haida\FilamentCommerceExperience\Policies\ExperienceBuyNowPreferencePolicy;
use Haida\FilamentCommerceExperience\Policies\ExperienceCsatSurveyPolicy;
use Haida\FilamentCommerceExperience\Policies\ExperienceQuestionPolicy;
use Haida\FilamentCommerceExperience\Policies\ExperienceReviewPolicy;
use Haida\FilamentCommerceExperience\Services\BuyNowService;
use Haida\FilamentCommerceExperience\Services\CsatSurveyService;
use Haida\FilamentCommerceExperience\Support\ExperienceCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentCommerceExperienceServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-commerce-experience')
            ->hasConfigFile('filament-commerce-experience')
            ->hasRoutes('api')
            ->hasTranslations()
            ->hasMigrations([
                '2026_01_02_000005_create_experience_tables',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(CsatSurveyService::class);
        $this->app->singleton(BuyNowService::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(ExperienceReview::class, ExperienceReviewPolicy::class);
        Gate::policy(ExperienceQuestion::class, ExperienceQuestionPolicy::class);
        Gate::policy(ExperienceCsatSurvey::class, ExperienceCsatSurveyPolicy::class);
        Gate::policy(ExperienceBuyNowPreference::class, ExperienceBuyNowPreferencePolicy::class);

        if (interface_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            ExperienceCapabilities::register($registry);
        }
    }
}
