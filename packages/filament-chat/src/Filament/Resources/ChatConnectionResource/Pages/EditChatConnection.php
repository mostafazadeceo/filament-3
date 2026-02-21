<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Filament\Resources\ChatConnectionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\FilamentChat\Filament\Resources\ChatConnectionResource;

class EditChatConnection extends EditRecord
{
    protected static string $resource = ChatConnectionResource::class;
}
