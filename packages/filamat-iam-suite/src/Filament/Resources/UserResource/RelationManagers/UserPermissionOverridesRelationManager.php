<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Support\AccessSettings;
use Filamat\IamSuite\Support\PermissionLabels;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserPermissionOverridesRelationManager extends RelationManager
{
    use InteractsWithTenant;

    protected static string $relationship = 'permissionOverrides';

    protected static ?string $title = 'بازنویسی دسترسی';

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('permission_label')
                    ->label('مجوز')
                    ->getStateUsing(fn ($record) => PermissionLabels::label($record->permission_key)),
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
}
