<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Filament\Resources\ChatConnectionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentChat\Filament\Resources\ChatConnectionResource;

class CreateChatConnection extends CreateRecord
{
    protected static string $resource = ChatConnectionResource::class;
}
