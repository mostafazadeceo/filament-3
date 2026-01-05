<?php

namespace Haida\FilamentRelograde\Resources\RelogradeConnectionResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filament\Actions\CreateAction;
use Haida\FilamentRelograde\Resources\RelogradeConnectionResource;
use Haida\FilamentRelograde\Support\RelogradeAuthorization;

class ListRelogradeConnections extends ListRecordsWithCreate
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
