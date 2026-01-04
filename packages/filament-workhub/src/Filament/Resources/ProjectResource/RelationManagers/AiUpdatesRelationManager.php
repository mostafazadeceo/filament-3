<?php

namespace Haida\FilamentWorkhub\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AiUpdatesRelationManager extends RelationManager
{
    protected static string $relationship = 'aiUpdates';

    protected static ?string $title = 'گزارش‌های هوش مصنوعی';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status_enum')->label('وضعیت'),
                TextColumn::make('body_markdown')
                    ->label('گزارش')
                    ->limit(120)
                    ->wrap()
                    ->copyable(),
                TextColumn::make('created_at')->label('ایجاد'),
            ])
            ->actions([])
            ->headerActions([]);
    }
}
