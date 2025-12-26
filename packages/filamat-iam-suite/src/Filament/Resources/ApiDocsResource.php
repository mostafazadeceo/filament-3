<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Models\ApiDoc;
use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Database\Eloquent\Model;
use ZPMLabs\FilamentApiDocsBuilder\Filament\Resources\ApiDocsResource\ApiDocsResource as BaseApiDocsResource;

class ApiDocsResource extends BaseApiDocsResource
{
    protected static ?string $model = ApiDoc::class;

    protected static ?string $navigationLabel = 'مستندات API';

    protected static ?string $pluralModelLabel = 'مستندات API';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-code-bracket';

    protected static string|\UnitEnum|null $navigationGroup = 'تنظیمات';

    public static function getModel(): string
    {
        return static::$model ?? parent::getModel();
    }

    public static function canViewAny(): bool
    {
        return IamAuthorization::allowsAny(['api.docs.view', 'api.view']);
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allowsAny(['api.docs.view', 'api.view'], IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allowsAny(['api.docs.manage', 'api.manage']);
    }

    public static function canEdit(Model $record): bool
    {
        return IamAuthorization::allowsAny(['api.docs.manage', 'api.manage'], IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canDelete(Model $record): bool
    {
        return IamAuthorization::allowsAny(['api.docs.manage', 'api.manage'], IamAuthorization::resolveTenantFromRecord($record));
    }
}
