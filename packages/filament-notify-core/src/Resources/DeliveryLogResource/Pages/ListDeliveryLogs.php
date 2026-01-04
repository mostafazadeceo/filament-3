<?php

namespace Haida\FilamentNotify\Core\Resources\DeliveryLogResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentNotify\Core\Resources\DeliveryLogResource;

class ListDeliveryLogs extends ListRecordsWithCreate
{
    protected static string $resource = DeliveryLogResource::class;
}
