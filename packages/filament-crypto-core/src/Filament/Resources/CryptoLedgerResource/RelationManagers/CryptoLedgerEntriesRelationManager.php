<?php

namespace Haida\FilamentCryptoCore\Filament\Resources\CryptoLedgerResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CryptoLedgerEntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'entries';

    protected static ?string $title = 'آرتیکل‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account.code')->label('کد حساب'),
                TextColumn::make('account.name_fa')->label('نام حساب'),
                TextColumn::make('debit')->label('بدهکار'),
                TextColumn::make('credit')->label('بستانکار'),
                TextColumn::make('currency')->label('ارز'),
                TextColumn::make('created_at')->label('ثبت')->jalaliDateTime(),
            ])
            ->defaultSort('id');
    }
}
