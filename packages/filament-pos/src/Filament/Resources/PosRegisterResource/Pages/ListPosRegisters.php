<?php

namespace Haida\FilamentPos\Filament\Resources\PosRegisterResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentPos\Filament\Resources\PosRegisterResource;

class ListPosRegisters extends ListRecordsWithCreate
{
    protected static string $resource = PosRegisterResource::class;
}
