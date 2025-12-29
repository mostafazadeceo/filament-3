<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\SeasonalReportResource\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeasonalSubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    protected static ?string $title = 'ارسال‌ها';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('submitted_at')
                    ->label('زمان ارسال'),
                TextInput::make('status')
                    ->label('وضعیت')
                    ->maxLength(64),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('submitted_at')->label('ارسال')->jalaliDateTime(),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ])
            ->defaultSort('submitted_at', 'desc');
    }
}
