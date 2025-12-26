<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\WebhookResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\WebhookResource;

class ListWebhooks extends ListRecordsWithCreate
{
    protected static string $resource = WebhookResource::class;
}
