<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapAudienceResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentMailtrap\Resources\MailtrapAudienceResource;

class ListMailtrapAudiences extends ListRecordsWithCreate
{
    protected static string $resource = MailtrapAudienceResource::class;
}
