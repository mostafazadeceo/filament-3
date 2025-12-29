<?php

namespace Haida\FilamentWorkhub\Filament\Resources\CustomFieldResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentWorkhub\Filament\Resources\CustomFieldResource;

class ListCustomFields extends ListRecordsWithCreate
{
    protected static string $resource = CustomFieldResource::class;
}
