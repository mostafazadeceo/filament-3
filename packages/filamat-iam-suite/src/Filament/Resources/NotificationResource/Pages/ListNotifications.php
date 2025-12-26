<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\NotificationResource\Pages;

use Filamat\IamSuite\Filament\Resources\NotificationResource;
use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;

class ListNotifications extends ListRecordsWithCreate
{
    protected static string $resource = NotificationResource::class;
}
