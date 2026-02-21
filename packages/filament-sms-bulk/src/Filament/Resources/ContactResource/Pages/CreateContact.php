<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\ContactResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\SmsBulk\Filament\Resources\ContactResource;

class CreateContact extends CreateRecord
{
    protected static string $resource = ContactResource::class;
}
