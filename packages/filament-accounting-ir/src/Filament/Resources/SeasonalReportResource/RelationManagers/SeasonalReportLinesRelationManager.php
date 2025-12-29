<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\SeasonalReportResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\Party;

class SeasonalReportLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'سطرهای گزارش';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('party_id')
                    ->label('طرف حساب')
                    ->options(fn () => Party::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                TextInput::make('invoice_no')
                    ->label('شماره فاکتور')
                    ->maxLength(64),
                DatePicker::make('invoice_date')
                    ->label('تاریخ فاکتور'),
                TextInput::make('amount')
                    ->label('مبلغ')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('tax_amount')
                    ->label('مالیات')
                    ->numeric()
                    ->minValue(0),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('party.name')->label('طرف حساب')->sortable(),
                TextColumn::make('invoice_no')->label('شماره'),
                TextColumn::make('invoice_date')->label('تاریخ')->jalaliDate(),
                TextColumn::make('amount')->label('مبلغ')->numeric(decimalPlaces: 0),
                TextColumn::make('tax_amount')->label('مالیات')->numeric(decimalPlaces: 0),
            ]);
    }
}
