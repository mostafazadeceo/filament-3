<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Pages;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Support\MegaSuperAdmin;
use Filament\Pages\Page;

class MegaAdminGuide extends Page
{
    use AuthorizesIam;

    protected static ?string $permission = 'iam.view';

    protected static ?string $navigationLabel = 'راهنمای مگا سوپرادمین';

    protected static ?string $title = 'راهنمای مگا سوپرادمین';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'مگا سوپر ادمین';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filamat-iam::pages.mega-admin-guide';

    public static function canAccess(): bool
    {
        return MegaSuperAdmin::check(auth()->user()) && parent::canAccess();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return MegaSuperAdmin::check(auth()->user());
    }
}
