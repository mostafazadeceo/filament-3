<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources\MailMailboxResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentMailOps\Filament\Resources\MailMailboxResource;

class ListMailMailboxes extends ListRecordsWithCreate
{
    protected static string $resource = MailMailboxResource::class;
}
