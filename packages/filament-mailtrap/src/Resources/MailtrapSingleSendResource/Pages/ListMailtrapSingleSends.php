<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapSingleSendResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentMailtrap\Resources\MailtrapSingleSendResource;

class ListMailtrapSingleSends extends ListRecordsWithCreate
{
    protected static string $resource = MailtrapSingleSendResource::class;
}
