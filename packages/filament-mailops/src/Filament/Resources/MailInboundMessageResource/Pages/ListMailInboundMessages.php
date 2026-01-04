<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources\MailInboundMessageResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Haida\FilamentMailOps\Filament\Resources\MailInboundMessageResource;

class ListMailInboundMessages extends ListRecords
{
    protected static string $resource = MailInboundMessageResource::class;
}
