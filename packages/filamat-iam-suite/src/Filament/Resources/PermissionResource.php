<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\PermissionResource\Pages\CreatePermission;
use Filamat\IamSuite\Filament\Resources\PermissionResource\Pages\EditPermission;
use Filamat\IamSuite\Filament\Resources\PermissionResource\Pages\ListPermissions;
use Filamat\IamSuite\Support\PermissionLabels;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission;

class PermissionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'iam';

    protected static ?string $model = Permission::class;

    protected static ?string $navigationLabel = 'مجوزها';

    protected static ?string $pluralModelLabel = 'مجوزها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت دسترسی';

    public static function getEloquentQuery(): Builder
    {
        return static::scopeByTenant(parent::getEloquentQuery());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')->label('کلید مجوز')->required(),
                TextInput::make('guard_name')->label('گارد')->default('web')->required(),
                static::tenantSelect(required: false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('عنوان فارسی')
                    ->getStateUsing(fn (Permission $record) => PermissionLabels::label($record->name)),
                TextColumn::make('name')->label('کلید')->searchable(),
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
            'index' => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'edit' => EditPermission::route('/{record}/edit'),
        ];
    }
}
