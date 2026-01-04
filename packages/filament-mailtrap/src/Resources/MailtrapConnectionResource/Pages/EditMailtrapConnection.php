<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapConnectionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\FilamentMailtrap\Resources\MailtrapConnectionResource;

class EditMailtrapConnection extends EditRecord
{
    protected static string $resource = MailtrapConnectionResource::class;
}
