<?php

namespace Haida\FilamentNotify\Core;

use Filament\Panel;

class FilamentNotifyManager
{
    /**
     * @var array<string, bool>
     */
    protected array $enabledPanels = [];

    public function registerPanel(Panel $panel): void
    {
        $this->enabledPanels[$panel->getId()] = true;
    }

    public function isPanelEnabled(?string $panelId): bool
    {
        if (! $panelId) {
            return false;
        }

        if (empty($this->enabledPanels)) {
            $configPanels = config('filament-notify.enabled_panels', []);
            if (! empty($configPanels)) {
                return in_array($panelId, $configPanels, true);
            }

            return true;
        }

        return isset($this->enabledPanels[$panelId]);
    }
}
