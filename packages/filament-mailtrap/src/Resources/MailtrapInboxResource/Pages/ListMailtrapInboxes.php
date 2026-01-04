<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapInboxResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentMailtrap\Resources\MailtrapInboxResource;

class ListMailtrapInboxes extends ListRecordsWithCreate
{
    protected static string $resource = MailtrapInboxResource::class;
}
