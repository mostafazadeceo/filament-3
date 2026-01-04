<?php

namespace Haida\FilamentRelograde\Resources\RelogradeWebhookEventResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentRelograde\Resources\RelogradeWebhookEventResource;

class ListRelogradeWebhookEvents extends ListRecordsWithCreate
{
    protected static string $resource = RelogradeWebhookEventResource::class;
}
