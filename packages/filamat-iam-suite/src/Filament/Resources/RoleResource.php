<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\RoleResource\Pages\CreateRole;
use Filamat\IamSuite\Filament\Resources\RoleResource\Pages\EditRole;
use Filamat\IamSuite\Filament\Resources\RoleResource\Pages\ListRoles;
use Filamat\IamSuite\Filament\Resources\RoleResource\RelationManagers\RolePermissionsRelationManager;
use Filamat\IamSuite\Filament\Resources\RoleResource\RelationManagers\RoleUsersRelationManager;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class RoleResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'iam';

    protected static ?string $model = Role::class;

    protected static ?string $navigationLabel = 'نقش‌ها';

    protected static ?string $pluralModelLabel = 'نقش‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت دسترسی';

    public static function getEloquentQuery(): Builder
    {
        return static::scopeByTenant(parent::getEloquentQuery());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')->label('نام نقش')->required(),
                TextInput::make('guard_name')->label('گارد')->default('web')->required(),
                static::tenantSelect(required: false),
                Textarea::make('reason')->label('دلیل تغییر')->required()->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('guard_name')
                    ->label('گارد')
                    ->formatStateUsing(fn (string $state) => $state === 'web' ? 'وب' : $state),
                TextColumn::make('tenant_id')->label('فضای کاری'),
                TextColumn::make('created_at')->label('ایجاد'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RolePermissionsRelationManager::class,
            RoleUsersRelationManager::class,
        ];
    }
}
