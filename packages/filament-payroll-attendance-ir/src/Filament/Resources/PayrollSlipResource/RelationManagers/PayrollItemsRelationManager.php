<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollSlipResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayrollItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('آیتم'),
                TextColumn::make('type')->label('نوع')->badge(),
                TextColumn::make('amount')->label('مبلغ'),
                TextColumn::make('tax_method')->label('مالیات')->badge(),
            ]);
    }
}
