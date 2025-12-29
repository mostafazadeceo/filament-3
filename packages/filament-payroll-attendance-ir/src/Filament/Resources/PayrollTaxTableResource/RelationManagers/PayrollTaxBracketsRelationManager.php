<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollTaxTableResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayrollTaxBracketsRelationManager extends RelationManager
{
    protected static string $relationship = 'brackets';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('min_amount')
                    ->label('حداقل')
                    ->numeric()
                    ->required(),
                TextInput::make('max_amount')
                    ->label('حداکثر')
                    ->numeric()
                    ->nullable(),
                TextInput::make('rate')
                    ->label('نرخ (%)')
                    ->numeric()
                    ->required(),
            ])
            ->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('min_amount')->label('حداقل'),
                TextColumn::make('max_amount')->label('حداکثر'),
                TextColumn::make('rate')->label('نرخ'),
            ]);
    }
}
