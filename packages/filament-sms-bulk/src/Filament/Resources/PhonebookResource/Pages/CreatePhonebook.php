<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\PhonebookResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\SmsBulk\Filament\Resources\PhonebookResource;

class CreatePhonebook extends CreateRecord
{
    protected static string $resource = PhonebookResource::class;
}
