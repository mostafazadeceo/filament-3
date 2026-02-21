<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\SenderIdentityResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\SmsBulk\Filament\Resources\SenderIdentityResource;

class CreateSenderIdentity extends CreateRecord
{
    protected static string $resource = SenderIdentityResource::class;
}
