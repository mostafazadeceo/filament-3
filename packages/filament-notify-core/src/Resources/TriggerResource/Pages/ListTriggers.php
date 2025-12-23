<?php

namespace Haida\FilamentNotify\Core\Resources\TriggerResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Haida\FilamentNotify\Core\Resources\TriggerResource;
use Illuminate\Support\Facades\Artisan;

class ListTriggers extends ListRecords
{
    protected static string $resource = TriggerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync')
                ->label('همگام‌سازی')
                ->action(function (): void {
                    Artisan::call('filament-notify:sync-triggers');
                    Notification::make()
                        ->title('تریگرها همگام‌سازی شدند.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
