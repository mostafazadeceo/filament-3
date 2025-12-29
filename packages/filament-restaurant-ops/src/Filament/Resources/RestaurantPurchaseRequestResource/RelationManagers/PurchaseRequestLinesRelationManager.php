<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources\RestaurantPurchaseRequestResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentRestaurantOps\Models\RestaurantItem;
use Haida\FilamentRestaurantOps\Models\RestaurantUom;

class PurchaseRequestLinesRelationManager extends RelationManager
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
                Textarea::make('notes')
                    ->label('توضیحات')
                    ->columnSpanFull(),
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
            ]);
    }
}
