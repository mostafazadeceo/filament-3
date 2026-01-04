<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapMessageResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentMailtrap\Resources\MailtrapMessageResource;

class ListMailtrapMessages extends ListRecordsWithCreate
{
    protected static string $resource = MailtrapMessageResource::class;
}
