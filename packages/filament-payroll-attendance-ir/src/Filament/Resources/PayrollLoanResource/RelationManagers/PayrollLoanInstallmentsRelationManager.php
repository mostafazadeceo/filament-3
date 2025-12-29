<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLoanResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayrollLoanInstallmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'installments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                DatePicker::make('due_date')
                    ->label('سررسید')
                    ->required(),
                TextInput::make('amount')
                    ->label('مبلغ')
                    ->numeric()
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'paid' => 'پرداخت شده',
                    ])
                    ->default('pending')
                    ->required(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('due_date')->label('سررسید')->jalaliDate(),
                TextColumn::make('amount')->label('مبلغ'),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('paid_at')->label('پرداخت')->jalaliDateTime(),
            ]);
    }
}
