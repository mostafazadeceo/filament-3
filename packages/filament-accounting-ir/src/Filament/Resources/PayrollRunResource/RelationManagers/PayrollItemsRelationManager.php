<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\PayrollRunResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\Employee;

class PayrollItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'اقلام حقوق';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->label('پرسنل')
                    ->options(fn () => Employee::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('gross')
                    ->label('ناخالص')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('tax')
                    ->label('مالیات')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('insurance')
                    ->label('بیمه')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('net')
                    ->label('خالص')
                    ->numeric()
                    ->minValue(0),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')->label('پرسنل')->sortable(),
                TextColumn::make('gross')->label('ناخالص')->numeric(decimalPlaces: 0),
                TextColumn::make('tax')->label('مالیات')->numeric(decimalPlaces: 0),
                TextColumn::make('insurance')->label('بیمه')->numeric(decimalPlaces: 0),
                TextColumn::make('net')->label('خالص')->numeric(decimalPlaces: 0),
            ]);
    }
}
