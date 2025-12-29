<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\ChequeResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChequeEventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    protected static ?string $title = 'رویدادها';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('event_date')
                    ->label('تاریخ')
                    ->required(),
                TextInput::make('status')
                    ->label('وضعیت')
                    ->maxLength(64)
                    ->required(),
                TextInput::make('notes')
                    ->label('یادداشت')
                    ->maxLength(255),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_date')->label('تاریخ')->jalaliDate(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('notes')->label('یادداشت'),
            ])
            ->defaultSort('event_date', 'desc');
    }
}
