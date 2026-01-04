<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources\MailOutboundMessageResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentMailOps\Filament\Resources\MailOutboundMessageResource;

class ListMailOutboundMessages extends ListRecordsWithCreate
{
    protected static string $resource = MailOutboundMessageResource::class;
}
