<?php

namespace Haida\CommerceOrders\Filament\Resources\CommerceOrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('کالا'),
                TextColumn::make('sku')->label('SKU'),
                TextColumn::make('quantity')->label('تعداد'),
                TextColumn::make('unit_price')->label('قیمت واحد'),
                TextColumn::make('line_total')->label('جمع'),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
