<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\QuietHoursProfileResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\SmsBulk\Filament\Resources\QuietHoursProfileResource;

class CreateQuietHoursProfile extends CreateRecord
{
    protected static string $resource = QuietHoursProfileResource::class;
}
