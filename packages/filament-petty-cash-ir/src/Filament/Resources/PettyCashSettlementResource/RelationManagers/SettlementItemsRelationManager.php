<?php

namespace Haida\FilamentPettyCashIr\Filament\Resources\PettyCashSettlementResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;

class SettlementItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('expense_id')
                    ->label('هزینه')
                    ->options(function (): array {
                        $settlement = $this->getOwnerRecord();

                        return PettyCashExpense::query()
                            ->where('fund_id', $settlement->fund_id)
                            ->where('status', PettyCashStatuses::EXPENSE_PAID)
                            ->whereDoesntHave('settlementItem')
                            ->orderByDesc('expense_date')
                            ->pluck('reference', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('expense.reference')->label('مرجع'),
                TextColumn::make('expense.amount')->label('مبلغ'),
                TextColumn::make('expense.expense_date')->label('تاریخ')->jalaliDate(),
                TextColumn::make('expense.status')->label('وضعیت')->badge(),
            ]);
    }
}
