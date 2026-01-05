<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Pages;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Support\OrganizationAccess;
use Filament\Pages\Page;

class OrganizationGuide extends Page
{
    use AuthorizesIam;

    protected static ?string $permission = 'iam.view';

    protected static ?string $navigationLabel = 'راهنمای سوپرادمین سازمان';

    protected static ?string $title = 'راهنمای سوپرادمین سازمان';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'راهنما';

    protected string $view = 'filamat-iam::pages.organization-guide';

    public static function canAccess(): bool
    {
        return OrganizationAccess::isCurrentOrganizationOwner() && parent::canAccess();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return OrganizationAccess::isCurrentOrganizationOwner();
    }
}
