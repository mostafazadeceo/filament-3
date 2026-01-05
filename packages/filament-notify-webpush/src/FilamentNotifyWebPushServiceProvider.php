<?php

namespace Haida\FilamentNotify\WebPush;

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Haida\FilamentNotify\Core\Channels\ChannelRegistry;
use Haida\FilamentNotify\WebPush\Channels\WebPushChannelDriver;
use Haida\FilamentNotify\WebPush\Support\VapidKeyManager;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentNotifyWebPushServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-notify-webpush')
            ->hasConfigFile('filament-notify-webpush')
            ->hasViews()
            ->hasMigrations([
                '2025_12_21_210000_create_fn_webpush_subscriptions_table',
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        app(ChannelRegistry::class)->register(new WebPushChannelDriver);

        Route::middleware(['web'])
            ->group(function (): void {
                Route::get(config('filament-notify-webpush.service_worker_path'), [\Haida\FilamentNotify\WebPush\Http\WebPushServiceWorkerController::class, 'show'])
                    ->name('filament-notify.webpush.sw');
            });

        Route::middleware(['web', 'auth'])
            ->group(function (): void {
                Route::post(config('filament-notify-webpush.subscribe_endpoint'), [\Haida\FilamentNotify\WebPush\Http\WebPushSubscriptionController::class, 'store'])
                    ->name('filament-notify.webpush.subscribe');
            });

        FilamentView::registerRenderHook(PanelsRenderHook::BODY_END, function () {
            $auth = Filament::auth();
            if (! $auth || ! $auth->check()) {
                return '';
            }

            $panelId = Filament::getCurrentPanel()?->getId();
            if (! $panelId) {
                return '';
            }

            $channelSettings = app(VapidKeyManager::class)->ensure($panelId);
            $vapidPublicKey = $channelSettings['vapid_public_key']
                ?? config('webpush.vapid.public_key')
                ?? config('filament-notify-webpush.vapid_public_key')
                ?? env('VAPID_PUBLIC_KEY');

            return view('filament-notify-webpush::partials.permission-prompt', [
                'settings' => $channelSettings,
                'vapidPublicKey' => $vapidPublicKey,
                'subscribeEndpoint' => config('filament-notify-webpush.subscribe_endpoint'),
                'serviceWorkerPath' => config('filament-notify-webpush.service_worker_path'),
                'userId' => $auth->user()?->getKey(),
            ]);
        });
    }
}
