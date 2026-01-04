<?php

declare(strict_types=1);

namespace Haida\FilamentNotify\WebPush\Support;

use Haida\FilamentNotify\Core\Models\ChannelSetting;
use Minishlink\WebPush\VAPID;
use Throwable;

class VapidKeyManager
{
    /**
     * @return array<string, mixed>
     */
    public function ensure(string $panelId): array
    {
        $settings = ChannelSetting::query()->firstOrCreate(
            ['panel_id' => $panelId, 'channel' => 'webpush'],
            ['settings' => []],
        );

        $data = $settings->settings ?? [];

        $publicKey = $data['vapid_public_key']
            ?? config('webpush.vapid.public_key')
            ?? config('filament-notify-webpush.vapid_public_key')
            ?? env('VAPID_PUBLIC_KEY');

        $privateKey = $data['vapid_private_key']
            ?? config('webpush.vapid.private_key')
            ?? env('VAPID_PRIVATE_KEY');

        if ($publicKey && $privateKey) {
            return $data;
        }

        if (! (bool) config('filament-notify-webpush.auto_generate_vapid', true)) {
            return $data;
        }

        try {
            $keys = VAPID::createVapidKeys();
        } catch (Throwable $exception) {
            return $data;
        }

        $data['vapid_public_key'] = $keys['publicKey'] ?? $publicKey;
        $data['vapid_private_key'] = $keys['privateKey'] ?? $privateKey;
        $data['vapid_subject'] = $data['vapid_subject']
            ?? config('filament-notify-webpush.vapid_subject')
            ?? config('webpush.vapid.subject')
            ?? env('VAPID_SUBJECT')
            ?? config('app.url');

        $settings->settings = $data;
        $settings->save();

        return $data;
    }
}
