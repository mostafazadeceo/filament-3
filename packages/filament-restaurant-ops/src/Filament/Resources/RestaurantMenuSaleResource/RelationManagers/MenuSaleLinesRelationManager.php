<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources\RestaurantMenuSaleResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuItem;

class MenuSaleLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('menu_item_id')
                    ->label('آیتم منو')
                    ->options(fn () => RestaurantMenuItem::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set): void {
                        if (! $state) {
                            return;
                        }

                        $price = RestaurantMenuItem::query()->whereKey($state)->value('price') ?? 0;
                        $set('unit_price', $price);
                        $set('line_total', $price);
                    })
                    ->required(),
                TextInput::make('quantity')
                    ->label('تعداد')
                    ->numeric()
                    ->default(1)
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set, $get): void {
                        $unitPrice = (float) ($get('unit_price') ?? 0);
                        $set('line_total', $unitPrice * (float) $state);
                    }),
                TextInput::make('unit_price')
                    ->label('قیمت واحد')
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set, $get): void {
                        $quantity = (float) ($get('quantity') ?? 0);
                        $set('line_total', $quantity * (float) $state);
                    }),
                TextInput::make('line_total')
                    ->label('جمع')
                    ->numeric()
                    ->default(0),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('menuItem.name')->label('آیتم منو'),
                TextColumn::make('quantity')->label('تعداد'),
                TextColumn::make('unit_price')->label('قیمت واحد'),
                TextColumn::make('line_total')->label('جمع'),
            ]);
    }
}
