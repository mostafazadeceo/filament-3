<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources\MailDomainResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\FilamentMailOps\Filament\Resources\MailDomainResource;

class EditMailDomain extends EditRecord
{
    protected static string $resource = MailDomainResource::class;
}
