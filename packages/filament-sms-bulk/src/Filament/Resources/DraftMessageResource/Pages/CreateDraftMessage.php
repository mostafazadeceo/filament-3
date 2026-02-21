<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\DraftMessageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\SmsBulk\Filament\Resources\DraftMessageResource;

class CreateDraftMessage extends CreateRecord
{
    protected static string $resource = DraftMessageResource::class;
}
