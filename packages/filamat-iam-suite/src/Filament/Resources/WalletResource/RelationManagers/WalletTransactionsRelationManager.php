<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\WalletResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WalletTransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $title = 'تراکنش‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('amount')->label('مبلغ')->numeric(),
                TextColumn::make('status')->label('وضعیت'),
                TextColumn::make('created_at')->label('ایجاد'),
            ]);
    }
}
