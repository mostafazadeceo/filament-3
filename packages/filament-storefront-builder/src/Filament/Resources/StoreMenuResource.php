<?php

namespace Haida\FilamentStorefrontBuilder\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentStorefrontBuilder\Filament\Resources\StoreMenuResource\Pages\CreateStoreMenu;
use Haida\FilamentStorefrontBuilder\Filament\Resources\StoreMenuResource\Pages\EditStoreMenu;
use Haida\FilamentStorefrontBuilder\Filament\Resources\StoreMenuResource\Pages\ListStoreMenus;
use Haida\FilamentStorefrontBuilder\Filament\Resources\StoreMenuResource\RelationManagers\StoreMenuItemsRelationManager;
use Haida\FilamentStorefrontBuilder\Models\StoreMenu;
use Illuminate\Database\Eloquent\Model;

class StoreMenuResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = StoreMenu::class;

    protected static ?string $modelLabel = 'منو';

    protected static ?string $pluralModelLabel = 'منوها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-3';

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
                TextInput::make('key')
                    ->label('کلید')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('active')
                    ->required(),
                Textarea::make('metadata')
                    ->label('متادیتا (JSON)')
                    ->rows(3)
                    ->nullable()
                    ->rules(['nullable', 'json'])
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }

                        return $state;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (! is_string($state) || trim($state) === '') {
                            return null;
                        }

                        $decoded = json_decode($state, true);

                        return is_array($decoded) ? $decoded : null;
                    }),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('کلید')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            StoreMenuItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStoreMenus::route('/'),
            'create' => CreateStoreMenu::route('/create'),
            'edit' => EditStoreMenu::route('/{record}/edit'),
        ];
    }
}
