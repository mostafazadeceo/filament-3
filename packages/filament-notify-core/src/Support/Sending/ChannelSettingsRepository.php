<?php

namespace Haida\FilamentNotify\Core\Support\Sending;

use Haida\FilamentNotify\Core\Models\ChannelSetting;

class ChannelSettingsRepository
{
    public function getSettings(string $panelId, string $channelKey): array
    {
        $settings = ChannelSetting::query()
            ->where('panel_id', $panelId)
            ->where('channel', $channelKey)
            ->first();

        return $settings?->settings ?? [];
    }
}
