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
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantRecipeResource\Pages\CreateRestaurantRecipe;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantRecipeResource\Pages\EditRestaurantRecipe;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantRecipeResource\Pages\ListRestaurantRecipes;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantRecipeResource\RelationManagers\RecipeLinesRelationManager;
use Haida\FilamentRestaurantOps\Models\RestaurantRecipe;
use Haida\FilamentRestaurantOps\Models\RestaurantUom;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class RestaurantRecipeResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'restaurant.recipe';

    protected static ?string $model = RestaurantRecipe::class;

    protected static ?string $modelLabel = 'فرمول تولید';

    protected static ?string $pluralModelLabel = 'فرمول‌های تولید';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static string|\UnitEnum|null $navigationGroup = 'کاست‌کنترل';

    protected static array $eagerLoad = ['yieldUom'];

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
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('کد')
                    ->maxLength(64),
                TextInput::make('yield_quantity')
                    ->label('مقدار خروجی')
                    ->numeric()
                    ->default(1),
                Select::make('yield_uom_id')
                    ->label('واحد خروجی')
                    ->options(fn () => RestaurantUom::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                TextInput::make('waste_percent')
                    ->label('درصد افت')
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
                TextColumn::make('yield_quantity')->label('خروجی'),
                TextColumn::make('yieldUom.name')->label('واحد'),
                IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            RecipeLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRestaurantRecipes::route('/'),
            'create' => CreateRestaurantRecipe::route('/create'),
            'edit' => EditRestaurantRecipe::route('/{record}/edit'),
        ];
    }
}
