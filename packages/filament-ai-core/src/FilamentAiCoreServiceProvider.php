<?php

namespace Haida\FilamentAiCore;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentAiCore\Models\AiPolicy;
use Haida\FilamentAiCore\Models\AiRequest;
use Haida\FilamentAiCore\Policies\AiPolicyPolicy;
use Haida\FilamentAiCore\Policies\AiRequestPolicy;
use Haida\FilamentAiCore\Services\AiCircuitBreaker;
use Haida\FilamentAiCore\Services\AiPolicyService;
use Haida\FilamentAiCore\Services\AiProviderManager;
use Haida\FilamentAiCore\Services\AiRateLimiter;
use Haida\FilamentAiCore\Services\AiRequestLogger;
use Haida\FilamentAiCore\Services\AiService;
use Haida\FilamentAiCore\Services\RedactionService;
use Haida\FilamentAiCore\Support\AiCoreCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentAiCoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-ai-core')
            ->hasConfigFile('filament-ai-core')
            ->hasTranslations()
            ->hasMigrations([
                '2026_02_01_000001_create_ai_policies_table',
                '2026_02_01_000002_create_ai_requests_table',
                '2026_02_01_000003_create_ai_feedback_table',
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(AiProviderManager::class);
        $this->app->singleton(AiPolicyService::class);
        $this->app->singleton(RedactionService::class);
        $this->app->singleton(AiRequestLogger::class);
        $this->app->singleton(AiRateLimiter::class);
        $this->app->singleton(AiCircuitBreaker::class);
        $this->app->singleton(AiService::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(AiPolicy::class, AiPolicyPolicy::class);
        Gate::policy(AiRequest::class, AiRequestPolicy::class);

        $registry = $this->app->make(CapabilityRegistryInterface::class);
        AiCoreCapabilities::register($registry);
    }
}
