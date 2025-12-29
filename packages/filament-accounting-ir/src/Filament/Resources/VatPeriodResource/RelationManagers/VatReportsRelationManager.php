<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\VatPeriodResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VatReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'reports';

    protected static ?string $title = 'اظهارنامه‌ها';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sales_base')->label('فروش')->numeric()->minValue(0),
                TextInput::make('sales_tax')->label('مالیات فروش')->numeric()->minValue(0),
                TextInput::make('purchase_base')->label('خرید')->numeric()->minValue(0),
                TextInput::make('purchase_tax')->label('مالیات خرید')->numeric()->minValue(0),
                TextInput::make('status')->label('وضعیت')->maxLength(64),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sales_base')->label('فروش')->numeric(decimalPlaces: 0),
                TextColumn::make('sales_tax')->label('مالیات فروش')->numeric(decimalPlaces: 0),
                TextColumn::make('purchase_base')->label('خرید')->numeric(decimalPlaces: 0),
                TextColumn::make('purchase_tax')->label('مالیات خرید')->numeric(decimalPlaces: 0),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
