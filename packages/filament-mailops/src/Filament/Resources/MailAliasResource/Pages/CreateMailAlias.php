<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources\MailAliasResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentMailOps\Filament\Resources\MailAliasResource;

class CreateMailAlias extends CreateRecord
{
    protected static string $resource = MailAliasResource::class;
}
