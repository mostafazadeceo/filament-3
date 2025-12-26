<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserApiKeysRelationManager extends RelationManager
{
    protected static string $relationship = 'apiKeys';

    protected static ?string $title = 'کلیدهای ای‌پی‌آی';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام'),
                TextColumn::make('token_prefix')->label('پیشوند'),
                TextColumn::make('last_used_at')->label('آخرین استفاده'),
                TextColumn::make('expires_at')->label('انقضا'),
            ]);
    }
}
