<?php

namespace Haida\FilamentStorefrontBuilder\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentStorefrontBuilder\Filament\Resources\StoreRedirectResource\Pages\CreateStoreRedirect;
use Haida\FilamentStorefrontBuilder\Filament\Resources\StoreRedirectResource\Pages\EditStoreRedirect;
use Haida\FilamentStorefrontBuilder\Filament\Resources\StoreRedirectResource\Pages\ListStoreRedirects;
use Haida\FilamentStorefrontBuilder\Models\StoreRedirect;
use Illuminate\Database\Eloquent\Model;

class StoreRedirectResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = StoreRedirect::class;

    protected static ?string $modelLabel = 'ریدایرکت';

    protected static ?string $pluralModelLabel = 'ریدایرکت‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static string|\UnitEnum|null $navigationGroup = 'سازنده فروشگاه';

    public static function canViewAny(): bool
    {
        return IamAuthorization::allowsAny(['storebuilder.view', 'storebuilder.manage']);
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allowsAny(['storebuilder.view', 'storebuilder.manage'], IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allows('storebuilder.manage');
    }

    public static function canEdit(Model $record): bool
    {
        return IamAuthorization::allows('storebuilder.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canDelete(Model $record): bool
    {
        return IamAuthorization::allows('storebuilder.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('from_path')
                    ->label('از مسیر')
                    ->required()
                    ->maxLength(255),
                TextInput::make('to_path')
                    ->label('به مسیر')
                    ->required()
                    ->maxLength(255),
                TextInput::make('status_code')
                    ->label('کد')
                    ->numeric()
                    ->default(301),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('active')
                    ->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from_path')
                    ->label('از مسیر')
                    ->searchable(),
                TextColumn::make('to_path')
                    ->label('به مسیر'),
                TextColumn::make('status_code')
                    ->label('کد'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStoreRedirects::route('/'),
            'create' => CreateStoreRedirect::route('/create'),
            'edit' => EditStoreRedirect::route('/{record}/edit'),
        ];
    }
}
