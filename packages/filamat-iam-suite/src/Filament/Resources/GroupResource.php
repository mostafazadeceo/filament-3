<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\GroupResource\Pages\CreateGroup;
use Filamat\IamSuite\Filament\Resources\GroupResource\Pages\EditGroup;
use Filamat\IamSuite\Filament\Resources\GroupResource\Pages\ListGroups;
use Filamat\IamSuite\Filament\Resources\GroupResource\RelationManagers\GroupPermissionsRelationManager;
use Filamat\IamSuite\Filament\Resources\GroupResource\RelationManagers\GroupRolesRelationManager;
use Filamat\IamSuite\Filament\Resources\GroupResource\RelationManagers\GroupUsersRelationManager;
use Filamat\IamSuite\Models\Group;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GroupResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'iam';

    protected static ?string $model = Group::class;

    protected static ?string $navigationLabel = 'گروه‌ها';

    protected static ?string $pluralModelLabel = 'گروه‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت دسترسی';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')->label('نام گروه')->required(),
                TextInput::make('description')->label('توضیح')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('tenant.name')->label('فضای کاری'),
                TextColumn::make('created_at')->label('ایجاد'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGroups::route('/'),
            'create' => CreateGroup::route('/create'),
            'edit' => EditGroup::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            GroupUsersRelationManager::class,
            GroupRolesRelationManager::class,
            GroupPermissionsRelationManager::class,
        ];
    }
}
