<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\WebhookResource\Pages;

use Filamat\IamSuite\Filament\Resources\WebhookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWebhook extends CreateRecord
{
    protected static string $resource = WebhookResource::class;
}
