<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Filament\Resources\ChatUserLinkResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Haida\FilamentChat\Filament\Resources\ChatUserLinkResource;

class ListChatUserLinks extends ListRecords
{
    protected static string $resource = ChatUserLinkResource::class;
}
