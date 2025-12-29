<?php

namespace Haida\FilamentWorkhub\Filament\Resources\StatusResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentWorkhub\Filament\Resources\StatusResource;

class ListStatuses extends ListRecordsWithCreate
{
    protected static string $resource = StatusResource::class;
}
