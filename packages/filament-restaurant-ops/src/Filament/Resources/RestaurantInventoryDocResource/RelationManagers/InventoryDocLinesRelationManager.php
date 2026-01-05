<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources\RestaurantInventoryDocResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentRestaurantOps\Models\RestaurantItem;
use Haida\FilamentRestaurantOps\Models\RestaurantUom;

class InventoryDocLinesRelationManager extends RelationManager
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

                        $uomId = RestaurantItem::query()->whereKey($state)->value('base_uom_id');
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
                TextInput::make('unit_cost')
                    ->label('قیمت واحد')
                    ->numeric()
                    ->default(0),
                TextInput::make('batch_no')
                    ->label('بچ')
                    ->maxLength(64)
                    ->required(fn (Get $get) => $this->requiresBatch($get('item_id'))),
                DatePicker::make('expires_at')
                    ->label('تاریخ انقضا')
                    ->nullable()
                    ->required(fn (Get $get) => $this->requiresExpiry($get('item_id'))),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.name')->label('کالا'),
                TextColumn::make('quantity')->label('مقدار'),
                TextColumn::make('unit_cost')->label('قیمت واحد'),
            ]);
    }

    protected function requiresBatch(?int $itemId): bool
    {
        if (! $itemId) {
            return false;
        }

        return (bool) RestaurantItem::query()
            ->whereKey($itemId)
            ->value('track_batch');
    }

    protected function requiresExpiry(?int $itemId): bool
    {
        if (! $itemId) {
            return false;
        }

        return (bool) RestaurantItem::query()
            ->whereKey($itemId)
            ->value('track_expiry');
    }
}
