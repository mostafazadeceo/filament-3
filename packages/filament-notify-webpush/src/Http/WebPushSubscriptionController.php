<?php

namespace Haida\FilamentNotify\WebPush\Http;

use Filament\Facades\Filament;
use Haida\FilamentNotify\Core\Models\ChannelSetting;
use Haida\FilamentNotify\Core\Support\Rendering\RenderedMessage;
use Haida\FilamentNotify\Core\Support\Testing\ChannelTestContextFactory;
use Haida\FilamentNotify\WebPush\Channels\WebPushChannelDriver;
use Haida\FilamentNotify\WebPush\Models\WebPushSubscription;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WebPushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $user = Filament::auth()?->user() ?? $request->user();
        if (! $user) {
            return response()->json(['message' => 'unauthenticated'], 401);
        }

        $panelId = Filament::getCurrentPanel()?->getId();
        $channelSettings = [];

        if ($panelId) {
            $settings = ChannelSetting::query()
                ->where('panel_id', $panelId)
                ->where('channel', 'webpush')
                ->first();
            $channelSettings = $settings?->settings ?? [];
        }

        $subscription = $request->input('subscription');
        if (! is_array($subscription)) {
            return response()->json(['message' => 'invalid_subscription'], 422);
        }

        $endpoint = $subscription['endpoint'] ?? null;
        $keys = $subscription['keys'] ?? [];

        if (! $endpoint || ! isset($keys['p256dh'], $keys['auth'])) {
            return response()->json(['message' => 'invalid_subscription'], 422);
        }

        $endpointHash = hash('sha256', $endpoint);

        $record = WebPushSubscription::updateOrCreate([
            'user_id' => $user->getKey(),
            'endpoint_hash' => $endpointHash,
        ], [
            'endpoint' => $endpoint,
            'public_key' => $keys['p256dh'],
            'auth_token' => $keys['auth'],
            'content_encoding' => $subscription['contentEncoding'] ?? null,
        ]);

        if (($channelSettings['welcome_enabled'] ?? false) && $record->wasRecentlyCreated) {
            $this->sendWelcomeNotification($user, $channelSettings, $panelId ?? 'admin');
        }

        return response()->json(['message' => 'subscribed']);
    }

    protected function sendWelcomeNotification(object $user, array $channelSettings, string $panelId): void
    {
        try {
            $driver = new WebPushChannelDriver;
            $context = ChannelTestContextFactory::make(
                $panelId,
                'webpush',
                $channelSettings,
                ['notifiable' => $user],
                [
                    'user' => $user,
                    'panel' => ['id' => $panelId],
                ],
            );

            $message = new RenderedMessage(
                subject: null,
                body: (string) ($channelSettings['welcome_body'] ?? 'وب‌پوش فعال شد.'),
                meta: [
                    'title' => $channelSettings['welcome_title'] ?? 'وب‌پوش فعال شد',
                    'url' => $channelSettings['welcome_url'] ?? null,
                    'icon' => $channelSettings['welcome_icon'] ?? null,
                ],
            );

            $driver->send($context, $message);
        } catch (\Throwable) {
            // Ignore welcome failures.
        }
    }
}
