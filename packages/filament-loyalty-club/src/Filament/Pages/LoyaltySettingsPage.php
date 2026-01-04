<?php

namespace Haida\FilamentLoyaltyClub\Filament\Pages;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filament\Pages\Page;

class LoyaltySettingsPage extends Page
{
    use AuthorizesIam;

    protected static ?string $permission = 'loyalty.settings.manage';

    protected static ?string $navigationLabel = 'تنظیمات';

    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه مشتریان';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament-loyalty-club::settings';
}
