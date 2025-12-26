<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserTokensRelationManager extends RelationManager
{
    protected static string $relationship = 'tokens';

    protected static ?string $title = 'نشست‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام'),
                TextColumn::make('last_used_at')->label('آخرین استفاده'),
                TextColumn::make('created_at')->label('ایجاد'),
            ])
            ->actions([
                DeleteAction::make()
                    ->label('لغو دسترسی')
                    ->visible(fn () => IamAuthorization::allows('iam.manage')),
            ]);
    }
}
