<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\PermissionOverrideResource\Pages\CreatePermissionOverride;
use Filamat\IamSuite\Filament\Resources\PermissionOverrideResource\Pages\EditPermissionOverride;
use Filamat\IamSuite\Filament\Resources\PermissionOverrideResource\Pages\ListPermissionOverrides;
use Filamat\IamSuite\Models\PermissionOverride;
use Filamat\IamSuite\Support\AccessSettings;
use Filamat\IamSuite\Support\PermissionLabels;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionOverrideResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'iam';

    protected static ?string $model = PermissionOverride::class;

    protected static ?string $navigationLabel = 'بازنویسی دسترسی';

    protected static ?string $pluralModelLabel = 'بازنویسی دسترسی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-vertical';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت دسترسی';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('user_id')
                    ->label('کاربر')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('permission_key')
                    ->label('مجوز')
                    ->searchable()
                    ->options(function () {
                        $tenant = TenantContext::getTenant();
                        $options = AccessSettings::permissionOptions($tenant);
                        $allowed = AccessSettings::personAllowedPermissions($tenant);
                        if ($allowed === []) {
                            $allowed = AccessSettings::companyAllowedPermissions($tenant);
                        }

                        return AccessSettings::filterOptions($options, $allowed);
                    })
                    ->required(),
                Select::make('effect')
                    ->label('اثر')
                    ->options([
                        'allow' => 'اجازه',
                        'deny' => 'عدم اجازه',
                    ])
                    ->required(),
                DateTimePicker::make('expires_at')->label('انقضا')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('فضای کاری'),
                TextColumn::make('user.name')->label('کاربر'),
                TextColumn::make('permission_label')
                    ->label('مجوز')
                    ->getStateUsing(fn (PermissionOverride $record) => PermissionLabels::label($record->permission_key)),
                TextColumn::make('permission_key')->label('کلید')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('effect')
                    ->label('اثر')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'allow' => 'اجازه',
                        'deny' => 'عدم اجازه',
                        default => $state,
                    }),
                TextColumn::make('expires_at')->label('انقضا'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermissionOverrides::route('/'),
            'create' => CreatePermissionOverride::route('/create'),
            'edit' => EditPermissionOverride::route('/{record}/edit'),
        ];
    }
}
