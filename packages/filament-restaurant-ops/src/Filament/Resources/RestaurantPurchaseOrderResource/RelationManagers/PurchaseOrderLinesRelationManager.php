<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources\RestaurantPurchaseOrderResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentRestaurantOps\Models\RestaurantItem;
use Haida\FilamentRestaurantOps\Models\RestaurantUom;

class PurchaseOrderLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('item_id')
                    ->label('کالا')
                    ->options(fn () => RestaurantItem::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set): void {
                        if (! $state) {
                            return;
                        }

                        $uomId = RestaurantItem::query()->whereKey($state)->value('purchase_uom_id');
                        $set('uom_id', $uomId);
                    })
                    ->required(),
                Select::make('uom_id')
                    ->label('واحد')
                    ->options(fn () => RestaurantUom::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                TextInput::make('quantity')
                    ->label('مقدار')
                    ->numeric()
                    ->required(),
                TextInput::make('unit_price')
                    ->label('قیمت واحد')
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_rate')
                    ->label('نرخ مالیات')
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_amount')
                    ->label('تخفیف')
                    ->numeric()
                    ->default(0),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.name')->label('کالا'),
                TextColumn::make('quantity')->label('مقدار'),
                TextColumn::make('unit_price')->label('قیمت واحد'),
                TextColumn::make('line_total')->label('جمع'),
            ]);
    }
}
