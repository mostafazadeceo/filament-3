<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\SubscriptionResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\SubscriptionResource;

class ListSubscriptions extends ListRecordsWithCreate
{
    protected static string $resource = SubscriptionResource::class;
}
