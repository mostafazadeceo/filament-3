<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserWalletsRelationManager extends RelationManager
{
    protected static string $relationship = 'wallets';

    protected static ?string $title = 'کیف پول‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('currency')->label('ارز'),
                TextColumn::make('balance')->label('موجودی')->numeric(),
                TextColumn::make('status')->label('وضعیت'),
            ]);
    }
}
