<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\WalletHoldResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\WalletHoldResource;

class ListWalletHolds extends ListRecordsWithCreate
{
    protected static string $resource = WalletHoldResource::class;
}
