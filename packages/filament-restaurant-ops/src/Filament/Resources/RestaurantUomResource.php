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
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantUomResource\Pages\CreateRestaurantUom;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantUomResource\Pages\EditRestaurantUom;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantUomResource\Pages\ListRestaurantUoms;
use Haida\FilamentRestaurantOps\Models\RestaurantUom;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class RestaurantUomResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'restaurant.uom';

    protected static ?string $model = RestaurantUom::class;

    protected static ?string $modelLabel = 'واحد';

    protected static ?string $pluralModelLabel = 'واحدها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static string|\UnitEnum|null $navigationGroup = 'اطلاعات پایه';

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
                TextInput::make('symbol')
                    ->label('نماد')
                    ->maxLength(32),
                Toggle::make('is_base')
                    ->label('واحد پایه')
                    ->default(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('symbol')->label('نماد'),
                IconColumn::make('is_base')->label('پایه')->boolean(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRestaurantUoms::route('/'),
            'create' => CreateRestaurantUom::route('/create'),
            'edit' => EditRestaurantUom::route('/{record}/edit'),
        ];
    }
}
