<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\SubscriptionPlanResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\SubscriptionPlanResource;

class ListSubscriptionPlans extends ListRecordsWithCreate
{
    protected static string $resource = SubscriptionPlanResource::class;
}
