<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources\MailDomainResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentMailOps\Filament\Resources\MailDomainResource;

class ListMailDomains extends ListRecordsWithCreate
{
    protected static string $resource = MailDomainResource::class;
}
