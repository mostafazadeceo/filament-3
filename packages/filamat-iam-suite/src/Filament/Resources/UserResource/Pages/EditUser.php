<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\UserResource\Pages;

use Filamat\IamSuite\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
}
