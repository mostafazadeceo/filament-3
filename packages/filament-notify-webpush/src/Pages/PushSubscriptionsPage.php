<?php

namespace Haida\FilamentNotify\WebPush\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Haida\FilamentNotify\WebPush\Support\VapidKeyManager;

class PushSubscriptionsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationLabel = 'وب‌پوش';

    protected static string|\UnitEnum|null $navigationGroup = 'اطلاع‌رسانی';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament-notify-webpush::pages.push-subscriptions';

    public function getViewData(): array
    {
        $panelId = Filament::getCurrentPanel()?->getId();
        $channelSettings = null;

        if ($panelId) {
            $channelSettings = app(VapidKeyManager::class)->ensure($panelId);
        }

        return [
            'vapidPublicKey' => $channelSettings['vapid_public_key'] ?? null
                ?? config('webpush.vapid.public_key')
                ?? config('filament-notify-webpush.vapid_public_key')
                ?? env('VAPID_PUBLIC_KEY'),
            'subscribeEndpoint' => config('filament-notify-webpush.subscribe_endpoint'),
            'serviceWorkerPath' => config('filament-notify-webpush.service_worker_path'),
        ];
    }
}
