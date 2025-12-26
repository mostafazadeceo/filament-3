<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\WalletResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\WalletResource;

class ListWallets extends ListRecordsWithCreate
{
    protected static string $resource = WalletResource::class;
}
