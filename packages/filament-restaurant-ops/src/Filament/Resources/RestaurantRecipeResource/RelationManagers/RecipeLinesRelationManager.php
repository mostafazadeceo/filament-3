<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources\RestaurantRecipeResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentRestaurantOps\Models\RestaurantItem;
use Haida\FilamentRestaurantOps\Models\RestaurantUom;

class RecipeLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('item_id')
                    ->label('کالا')
                    ->options(fn () => RestaurantItem::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('uom_id')
                    ->label('واحد')
                    ->options(fn () => RestaurantUom::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                TextInput::make('quantity')
                    ->label('مقدار')
                    ->numeric()
                    ->required(),
                TextInput::make('waste_percent')
                    ->label('درصد افت')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_optional')
                    ->label('اختیاری')
                    ->default(false),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.name')->label('کالا'),
                TextColumn::make('quantity')->label('مقدار'),
                TextColumn::make('uom.name')->label('واحد'),
                TextColumn::make('waste_percent')->label('افت'),
                IconColumn::make('is_optional')->label('اختیاری')->boolean(),
            ]);
    }
}
