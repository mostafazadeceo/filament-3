<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\JournalEntryResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\ChartAccount;

class JournalLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'ردیف‌های سند';

    protected static ?string $modelLabel = 'ردیف سند';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('account_id')
                    ->label('حساب')
                    ->options(fn () => ChartAccount::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('description')
                    ->label('شرح')
                    ->maxLength(255),
                TextInput::make('debit')
                    ->label('بدهکار')
                    ->numeric()
                    ->minValue(0)
                    ->rule('required_without:credit'),
                TextInput::make('credit')
                    ->label('بستانکار')
                    ->numeric()
                    ->minValue(0)
                    ->rule('required_without:debit'),
                TextInput::make('currency')
                    ->label('ارز')
                    ->default('IRR')
                    ->maxLength(8),
                TextInput::make('amount')
                    ->label('مبلغ ارزی')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('exchange_rate')
                    ->label('نرخ تسعیر')
                    ->numeric()
                    ->minValue(0),
                TagsInput::make('dimensions')
                    ->label('ابعاد')
                    ->placeholder('cost_center:1, project:3'),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account.name')->label('حساب')->sortable(),
                TextColumn::make('description')->label('شرح')->limit(40),
                TextColumn::make('debit')->label('بدهکار')->numeric(decimalPlaces: 0),
                TextColumn::make('credit')->label('بستانکار')->numeric(decimalPlaces: 0),
                TextColumn::make('currency')->label('ارز'),
            ]);
    }
}
