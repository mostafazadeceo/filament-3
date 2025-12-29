<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollRunResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayrollSlipsRelationManager extends RelationManager
{
    protected static string $relationship = 'slips';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('پرسنل')
                    ->formatStateUsing(fn ($state, $record) => trim($record->employee?->first_name.' '.$record->employee?->last_name)),
                TextColumn::make('scope')->label('نوع')->badge(),
                TextColumn::make('gross_amount')->label('ناخالص'),
                TextColumn::make('net_amount')->label('خالص'),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ]);
    }
}
