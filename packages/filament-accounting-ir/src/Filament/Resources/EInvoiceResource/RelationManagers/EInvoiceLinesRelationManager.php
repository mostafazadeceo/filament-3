<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EInvoiceLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'اقلام';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('description')->label('شرح')->maxLength(255),
                TextInput::make('quantity')->label('تعداد')->numeric()->minValue(0),
                TextInput::make('unit_price')->label('مبلغ واحد')->numeric()->minValue(0),
                TextInput::make('tax_amount')->label('مالیات')->numeric()->minValue(0),
                TextInput::make('line_total')->label('جمع')->numeric()->minValue(0),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')->label('شرح')->searchable(),
                TextColumn::make('quantity')->label('تعداد')->numeric(decimalPlaces: 2),
                TextColumn::make('unit_price')->label('مبلغ واحد')->numeric(decimalPlaces: 0),
                TextColumn::make('tax_amount')->label('مالیات')->numeric(decimalPlaces: 0),
                TextColumn::make('line_total')->label('جمع')->numeric(decimalPlaces: 0),
            ]);
    }
}
