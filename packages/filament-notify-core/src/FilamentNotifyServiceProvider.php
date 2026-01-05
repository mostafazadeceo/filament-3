<?php

namespace Haida\FilamentNotify\Core;

use Filament\Actions\Events\ActionCalled;
use Haida\FilamentNotify\Core\Channels\ChannelRegistry;
use Haida\FilamentNotify\Core\Channels\MailChannelDriver;
use Haida\FilamentNotify\Core\Commands\SyncTriggersCommand;
use Haida\FilamentNotify\Core\Listeners\ActionCalledListener;
use Haida\FilamentNotify\Core\Listeners\EloquentEventListener;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentNotifyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-notify-core')
            ->hasConfigFile('filament-notify')
            ->hasViews()
            ->hasMigrations([
                '2025_12_21_200000_create_fn_triggers_table',
                '2025_12_21_200001_create_fn_templates_table',
                '2025_12_21_200002_create_fn_notification_rules_table',
                '2025_12_21_200003_create_fn_delivery_logs_table',
                '2025_12_21_200004_create_fn_channel_settings_table',
            ])
            ->runsMigrations()
            ->hasTranslations()
            ->hasCommands([
                SyncTriggersCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ChannelRegistry::class);
        $this->app->singleton(FilamentNotifyManager::class);
    }

    public function packageBooted(): void
    {
        app(ChannelRegistry::class)->register(new MailChannelDriver);

        Event::listen(ActionCalled::class, ActionCalledListener::class);

        if (config('filament-notify.enable_eloquent_events')) {
            Event::listen('eloquent.*', EloquentEventListener::class);
        }
    }
}
