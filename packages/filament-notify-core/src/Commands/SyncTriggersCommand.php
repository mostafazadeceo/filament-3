<?php

namespace Haida\FilamentNotify\Core\Commands;

use Filament\Facades\Filament;
use Haida\FilamentNotify\Core\Models\Trigger;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncTriggersCommand extends Command
{
    protected $signature = 'filament-notify:sync-triggers {--panel=}';

    protected $description = 'Sync Filament notification triggers.';

    public function handle(): int
    {
        $panelOption = $this->option('panel');
        $panels = $panelOption ? [Filament::getPanel($panelOption)] : Filament::getPanels();

        foreach ($panels as $panel) {
            if (! $panel) {
                continue;
            }

            $panelId = $panel->getId();
            foreach ($panel->getResources() as $resourceClass) {
                $modelClass = $resourceClass::getModel();
                $resourceLabel = class_basename($resourceClass);

                foreach (['created', 'updated', 'deleted', 'restored'] as $event) {
                    $key = sprintf('filament.resource.%s.%s', $resourceClass, $event);
                    $label = sprintf('%s - %s', $resourceLabel, Str::headline($event));

                    Trigger::updateOrCreate(
                        [
                            'panel_id' => $panelId,
                            'key' => $key,
                        ],
                        [
                            'label' => $label,
                            'type' => 'manual',
                            'meta' => [
                                'resource' => $resourceClass,
                                'model' => $modelClass,
                                'event' => $event,
                            ],
                        ],
                    );
                }
            }
        }

        $this->info('Triggers synced.');

        return self::SUCCESS;
    }
}
