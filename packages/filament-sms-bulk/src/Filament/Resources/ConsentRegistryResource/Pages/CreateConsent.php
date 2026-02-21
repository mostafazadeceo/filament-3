<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\ConsentRegistryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\SmsBulk\Filament\Resources\ConsentRegistryResource;

class CreateConsent extends CreateRecord
{
    protected static string $resource = ConsentRegistryResource::class;
}
