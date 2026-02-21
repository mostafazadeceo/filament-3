<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Filament\Resources\ChatConnectionResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentChat\Filament\Resources\ChatConnectionResource;

class ListChatConnections extends ListRecordsWithCreate
{
    protected static string $resource = ChatConnectionResource::class;
}
