<?php

namespace Haida\CommerceOrders\Filament\Resources\CommerceOrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderPaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'method';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('method')->label('روش'),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('amount')->label('مبلغ'),
                TextColumn::make('currency')->label('ارز'),
                TextColumn::make('provider')->label('درگاه'),
                TextColumn::make('reference')->label('مرجع'),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
