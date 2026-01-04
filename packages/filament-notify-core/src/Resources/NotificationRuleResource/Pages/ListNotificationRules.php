<?php

namespace Haida\FilamentNotify\Core\Resources\NotificationRuleResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentNotify\Core\Resources\NotificationRuleResource;

class ListNotificationRules extends ListRecordsWithCreate
{
    protected static string $resource = NotificationRuleResource::class;
}
