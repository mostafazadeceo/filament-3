<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\PrivilegeEligibilityResource\Pages\CreatePrivilegeEligibility;
use Filamat\IamSuite\Filament\Resources\PrivilegeEligibilityResource\Pages\EditPrivilegeEligibility;
use Filamat\IamSuite\Filament\Resources\PrivilegeEligibilityResource\Pages\ListPrivilegeEligibilities;
use Filamat\IamSuite\Models\PrivilegeEligibility;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PrivilegeEligibilityResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'pam';

    protected static ?string $model = PrivilegeEligibility::class;

    protected static ?string $navigationLabel = 'واجدین نقش ممتاز';

    protected static ?string $pluralModelLabel = 'واجدین نقش ممتاز';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت دسترسی';

    public static function form(Schema $schema): Schema
    {
        $userModel = config('auth.providers.users.model');

        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('user_id')
                    ->label('کاربر')
                    ->options(fn () => $userModel::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('role_id')
                    ->label('نقش')
                    ->relationship('role', 'name')
                    ->searchable()
                    ->required(),
                Toggle::make('can_request')->label('اجازه درخواست')->default(true),
                Toggle::make('active')->label('فعال')->default(true),
                Textarea::make('reason')->label('دلیل')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('فضای کاری'),
                TextColumn::make('user.name')->label('کاربر')->searchable(),
                TextColumn::make('role.name')->label('نقش'),
                ToggleColumn::make('can_request')->label('اجازه درخواست'),
                ToggleColumn::make('active')->label('فعال'),
                TextColumn::make('created_at')->label('ایجاد'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrivilegeEligibilities::route('/'),
            'create' => CreatePrivilegeEligibility::route('/create'),
            'edit' => EditPrivilegeEligibility::route('/{record}/edit'),
        ];
    }
}
