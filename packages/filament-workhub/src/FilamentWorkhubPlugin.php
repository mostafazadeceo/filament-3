<?php

namespace Haida\FilamentWorkhub;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentWorkhub\Filament\Pages\KanbanBoardPage;
use Haida\FilamentWorkhub\Filament\Resources\AutomationRuleResource;
use Haida\FilamentWorkhub\Filament\Resources\CustomFieldResource;
use Haida\FilamentWorkhub\Filament\Resources\LabelResource;
use Haida\FilamentWorkhub\Filament\Resources\ProjectResource;
use Haida\FilamentWorkhub\Filament\Resources\StatusResource;
use Haida\FilamentWorkhub\Filament\Resources\TransitionResource;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource;
use Haida\FilamentWorkhub\Filament\Resources\WorkTypeResource;
use Haida\FilamentWorkhub\Filament\Resources\WorkflowResource;

class FilamentWorkhubPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'workhub';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                ProjectResource::class,
                WorkItemResource::class,
                WorkflowResource::class,
                StatusResource::class,
                TransitionResource::class,
                WorkTypeResource::class,
                LabelResource::class,
                CustomFieldResource::class,
                AutomationRuleResource::class,
            ])
            ->pages([
                KanbanBoardPage::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op for now.
    }
}
