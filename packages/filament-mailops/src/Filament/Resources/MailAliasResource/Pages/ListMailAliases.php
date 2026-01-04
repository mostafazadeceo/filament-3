<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources\MailAliasResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentMailOps\Filament\Resources\MailAliasResource;

class ListMailAliases extends ListRecordsWithCreate
{
    protected static string $resource = MailAliasResource::class;
}
