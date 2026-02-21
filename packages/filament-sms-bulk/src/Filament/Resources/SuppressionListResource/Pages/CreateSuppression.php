<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\SuppressionListResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\SmsBulk\Filament\Resources\SuppressionListResource;

class CreateSuppression extends CreateRecord
{
    protected static string $resource = SuppressionListResource::class;
}
