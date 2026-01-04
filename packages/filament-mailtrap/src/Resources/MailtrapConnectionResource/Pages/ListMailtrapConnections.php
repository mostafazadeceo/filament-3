<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapConnectionResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentMailtrap\Resources\MailtrapConnectionResource;

class ListMailtrapConnections extends ListRecordsWithCreate
{
    protected static string $resource = MailtrapConnectionResource::class;
}
