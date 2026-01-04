<?php

namespace Haida\FilamentCommerceCore\Filament\Resources\CommerceProductResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceProductResource;

class ListCommerceProducts extends ListRecordsWithCreate
{
    protected static string $resource = CommerceProductResource::class;
}
