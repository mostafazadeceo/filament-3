<?php

namespace Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AuditEventsRelationManager extends RelationManager
{
    protected static string $relationship = 'auditEvents';

    protected static ?string $title = 'تاریخچه';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event')->label('رویداد'),
                TextColumn::make('actor.name')->label('کاربر'),
                TextColumn::make('created_at')->label('زمان'),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with('actor'))
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
