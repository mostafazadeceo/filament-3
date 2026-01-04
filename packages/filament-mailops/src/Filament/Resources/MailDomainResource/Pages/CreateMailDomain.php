<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources\MailDomainResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentMailOps\Filament\Resources\MailDomainResource;

class CreateMailDomain extends CreateRecord
{
    protected static string $resource = MailDomainResource::class;
}
