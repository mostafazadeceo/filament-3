<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapConnectionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentMailtrap\Resources\MailtrapConnectionResource;

class CreateMailtrapConnection extends CreateRecord
{
    protected static string $resource = MailtrapConnectionResource::class;
}
