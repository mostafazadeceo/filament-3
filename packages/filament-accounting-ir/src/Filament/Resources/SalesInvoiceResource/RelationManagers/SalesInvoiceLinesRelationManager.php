<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\SalesInvoiceResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\ProductService;

class SalesInvoiceLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'اقلام فاکتور';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('کالا/خدمت')
                    ->options(fn () => ProductService::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                TextInput::make('description')
                    ->label('شرح')
                    ->maxLength(255),
                TextInput::make('quantity')
                    ->label('تعداد')
                    ->numeric()
                    ->minValue(0)
                    ->default(1),
                TextInput::make('unit_price')
                    ->label('قیمت واحد')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('discount_amount')
                    ->label('تخفیف')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('tax_rate')
                    ->label('نرخ مالیات')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('tax_amount')
                    ->label('مالیات')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('line_total')
                    ->label('جمع')
                    ->numeric()
                    ->minValue(0),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->label('کالا/خدمت'),
                TextColumn::make('quantity')->label('تعداد')->numeric(),
                TextColumn::make('unit_price')->label('قیمت')->numeric(),
                TextColumn::make('line_total')->label('جمع')->numeric(),
            ]);
    }
}
