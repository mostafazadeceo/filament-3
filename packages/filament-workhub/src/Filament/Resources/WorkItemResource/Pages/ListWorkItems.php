<?php

namespace Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource;

class ListWorkItems extends ListRecordsWithCreate
{
    protected static string $resource = WorkItemResource::class;
}
