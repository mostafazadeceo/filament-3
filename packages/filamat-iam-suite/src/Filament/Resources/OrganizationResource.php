<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Resources\OrganizationResource\Pages\CreateOrganization;
use Filamat\IamSuite\Filament\Resources\OrganizationResource\Pages\EditOrganization;
use Filamat\IamSuite\Filament\Resources\OrganizationResource\Pages\ListOrganizations;
use Filamat\IamSuite\Models\Organization;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrganizationResource extends IamResource
{
    protected static ?string $permissionPrefix = 'iam';

    protected static ?string $model = Organization::class;

    protected static ?string $navigationLabel = 'سازمان‌ها';

    protected static ?string $pluralModelLabel = 'سازمان‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت کلان';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')->label('نام')->required(),
                Select::make('owner_user_id')
                    ->label('مالک')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                Select::make('shared_data_mode')
                    ->label('حالت داده مشترک')
                    ->options([
                        'isolated' => 'ایزوله',
                        'shared_by_organization' => 'اشتراکی در سازمان',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('shared_data_mode')->label('حالت اشتراک'),
                TextColumn::make('created_at')->label('ایجاد'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizations::route('/'),
            'create' => CreateOrganization::route('/create'),
            'edit' => EditOrganization::route('/{record}/edit'),
        ];
    }
}
