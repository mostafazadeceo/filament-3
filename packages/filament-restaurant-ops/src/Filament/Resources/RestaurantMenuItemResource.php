<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentRestaurantOps\Filament\Resources\Concerns\HasEagerLoads;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantMenuItemResource\Pages\CreateRestaurantMenuItem;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantMenuItemResource\Pages\EditRestaurantMenuItem;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantMenuItemResource\Pages\ListRestaurantMenuItems;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuItem;
use Haida\FilamentRestaurantOps\Models\RestaurantRecipe;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class RestaurantMenuItemResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'restaurant.menu_item';

    protected static ?string $model = RestaurantMenuItem::class;

    protected static ?string $modelLabel = 'آیتم منو';

    protected static ?string $pluralModelLabel = 'آیتم‌های منو';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'کاست‌کنترل';

    protected static array $eagerLoad = ['recipe'];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(fn () => AccountingCompany::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('recipe_id')
                    ->label('فرمول تولید')
                    ->options(fn () => RestaurantRecipe::query()->where('is_active', true)->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('کد')
                    ->maxLength(64),
                TextInput::make('category')
                    ->label('دسته')
                    ->maxLength(255),
                TextInput::make('price')
                    ->label('قیمت فروش')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('category')->label('دسته'),
                TextColumn::make('recipe.name')->label('فرمول تولید'),
                TextColumn::make('price')->label('قیمت'),
                IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRestaurantMenuItems::route('/'),
            'create' => CreateRestaurantMenuItem::route('/create'),
            'edit' => EditRestaurantMenuItem::route('/{record}/edit'),
        ];
    }
}
