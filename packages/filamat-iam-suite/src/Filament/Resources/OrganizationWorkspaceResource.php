<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Resources\OrganizationWorkspaceResource\Pages\CreateOrganizationWorkspace;
use Filamat\IamSuite\Filament\Resources\OrganizationWorkspaceResource\Pages\EditOrganizationWorkspace;
use Filamat\IamSuite\Filament\Resources\OrganizationWorkspaceResource\Pages\ListOrganizationWorkspaces;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\ModuleCatalog;
use Filamat\IamSuite\Services\OrganizationEntitlementService;
use Filamat\IamSuite\Support\OrganizationAccess;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrganizationWorkspaceResource extends IamResource
{
    protected static ?string $permissionPrefix = 'tenant';

    protected static ?string $model = Tenant::class;

    protected static ?string $navigationLabel = 'فضاهای کاری سازمان';

    protected static ?string $pluralModelLabel = 'فضاهای کاری سازمان';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت سازمان';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            return $query->whereRaw('1=0');
        }

        return $query->where('organization_id', $tenant->organization_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Wizard::make([
                    Step::make('فضای کاری')
                        ->schema([
                            Hidden::make('organization_id')
                                ->default(fn () => TenantContext::getTenant()?->organization_id)
                                ->dehydrated(),
                            TextInput::make('name')->label('نام')->required(),
                            TextInput::make('slug')->label('شناسه')->required(),
                            Select::make('owner_user_id')
                                ->label('مالک')
                                ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray())
                                ->searchable(),
                            Select::make('status')
                                ->label('وضعیت')
                                ->options([
                                    'active' => 'فعال',
                                    'inactive' => 'غیرفعال',
                                ])
                                ->required(),
                            TextInput::make('locale')->label('زبان')->nullable(),
                            TextInput::make('timezone')->label('منطقه زمانی')->nullable(),
                        ]),
                    Step::make('ماژول‌ها')
                        ->schema([
                            CheckboxList::make('settings.access.modules')
                                ->label('ماژول‌های فعال')
                                ->options(function () {
                                    $tenant = TenantContext::getTenant();
                                    if (! $tenant) {
                                        return [];
                                    }

                                    $options = app(ModuleCatalog::class)->moduleOptions();
                                    $allowed = app(OrganizationEntitlementService::class)->allowedModulesForTenant($tenant);
                                    $allowedMap = array_fill_keys($allowed, true);

                                    return array_intersect_key($options, $allowedMap);
                                })
                                ->columns(2)
                                ->searchable(),
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('slug')->label('شناسه')->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                        default => $state,
                    }),
                TextColumn::make('created_at')->label('ایجاد'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizationWorkspaces::route('/'),
            'create' => CreateOrganizationWorkspace::route('/create'),
            'edit' => EditOrganizationWorkspace::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return parent::canViewAny() && OrganizationAccess::isCurrentOrganizationOwner();
    }

    public static function canCreate(): bool
    {
        $organization = OrganizationAccess::currentOrganization();
        if (! $organization) {
            return false;
        }

        if (! parent::canCreate()) {
            return false;
        }

        if (! OrganizationAccess::isOrganizationOwner($organization)) {
            return false;
        }

        return app(OrganizationEntitlementService::class)->canCreateTenant($organization);
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return parent::canEdit($record) && OrganizationAccess::isCurrentOrganizationOwner();
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return parent::canDelete($record) && OrganizationAccess::isCurrentOrganizationOwner();
    }
}
