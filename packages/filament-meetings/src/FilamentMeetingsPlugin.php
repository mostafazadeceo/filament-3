<?php

namespace Haida\FilamentMeetings;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentMeetings\Filament\Pages\MeetingRoomPage;
use Haida\FilamentMeetings\Filament\Resources\MeetingResource;
use Haida\FilamentMeetings\Filament\Resources\MeetingTemplateResource;
use Haida\FilamentMeetings\Filament\Widgets\MyMeetingActionItemsWidget;
use Haida\FilamentMeetings\Filament\Widgets\TodayMeetingsWidget;

class FilamentMeetingsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'meetings';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                MeetingResource::class,
                MeetingTemplateResource::class,
            ])
            ->pages([
                MeetingRoomPage::class,
            ])
            ->widgets([
                TodayMeetingsWidget::class,
                MyMeetingActionItemsWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
