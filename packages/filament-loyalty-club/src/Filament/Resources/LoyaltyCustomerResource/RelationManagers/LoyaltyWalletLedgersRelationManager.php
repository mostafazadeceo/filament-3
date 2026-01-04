<?php

namespace Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyCustomerResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LoyaltyWalletLedgersRelationManager extends RelationManager
{
    protected static string $relationship = 'ledgers';

    protected static ?string $title = 'گردش امتیاز';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('points_delta')->label('امتیاز'),
                TextColumn::make('cashback_delta')->label('کش‌بک'),
                TextColumn::make('balance_after_points')->label('مانده امتیاز'),
                TextColumn::make('balance_after_cashback')->label('مانده کش‌بک'),
                TextColumn::make('created_at')->label('تاریخ'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
