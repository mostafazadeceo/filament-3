<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\TaxRateResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaxRateVersionsRelationManager extends RelationManager
{
    protected static string $relationship = 'versions';

    protected static ?string $title = 'نسخه‌های نرخ';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('rate')
                    ->label('نرخ')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                DatePicker::make('effective_from')
                    ->label('از تاریخ'),
                DatePicker::make('effective_to')
                    ->label('تا تاریخ'),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rate')->label('نرخ')->numeric(decimalPlaces: 4),
                TextColumn::make('effective_from')->label('از')->jalaliDate(),
                TextColumn::make('effective_to')->label('تا')->jalaliDate(),
            ])
            ->defaultSort('effective_from', 'desc');
    }
}
