<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapSendingDomainResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentMailtrap\Resources\MailtrapSendingDomainResource;

class ListMailtrapSendingDomains extends ListRecordsWithCreate
{
    protected static string $resource = MailtrapSendingDomainResource::class;
}
