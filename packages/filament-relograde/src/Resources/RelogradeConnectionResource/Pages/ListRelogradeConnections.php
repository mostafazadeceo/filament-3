<?php

namespace Haida\FilamentRelograde\Resources\RelogradeConnectionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Haida\FilamentRelograde\Resources\RelogradeConnectionResource;
use Haida\FilamentRelograde\Support\RelogradeAuthorization;

class ListRelogradeConnections extends ListRecords
{
    protected static string $resource = RelogradeConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('ایجاد اتصال')
                ->visible(fn () => RelogradeAuthorization::can('settings_manage')),
        ];
    }
}
