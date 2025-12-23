<?php

namespace Haida\FilamentNotify\WebPush;

use Haida\FilamentNotify\Core\Channels\ChannelRegistry;
use Haida\FilamentNotify\Core\Models\ChannelSetting;
use Haida\FilamentNotify\WebPush\Channels\WebPushChannelDriver;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
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
        app(ChannelRegistry::class)->register(new WebPushChannelDriver());

        Route::middleware(['web', 'auth'])
            ->group(function (): void {
                Route::post(config('filament-notify-webpush.subscribe_endpoint'), [\Haida\FilamentNotify\WebPush\Http\WebPushSubscriptionController::class, 'store'])
                    ->name('filament-notify.webpush.subscribe');

                Route::get(config('filament-notify-webpush.service_worker_path'), [\Haida\FilamentNotify\WebPush\Http\WebPushServiceWorkerController::class, 'show'])
                    ->name('filament-notify.webpush.sw');
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

            $settings = ChannelSetting::query()
                ->where('panel_id', $panelId)
                ->where('channel', 'webpush')
                ->first();

            $channelSettings = $settings?->settings ?? [];
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
