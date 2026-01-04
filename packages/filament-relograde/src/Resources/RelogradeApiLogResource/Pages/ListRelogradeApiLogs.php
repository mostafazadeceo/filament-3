<?php

namespace Haida\FilamentRelograde\Resources\RelogradeApiLogResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentRelograde\Resources\RelogradeApiLogResource;

class ListRelogradeApiLogs extends ListRecordsWithCreate
{
    protected static string $resource = RelogradeApiLogResource::class;
}
