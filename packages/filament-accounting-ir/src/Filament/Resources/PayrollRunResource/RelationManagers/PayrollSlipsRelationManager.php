<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\PayrollRunResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\Employee;

class PayrollSlipsRelationManager extends RelationManager
{
    protected static string $relationship = 'slips';

    protected static ?string $title = 'فیش حقوق';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->label('پرسنل')
                    ->options(fn () => Employee::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')->label('پرسنل')->sortable(),
                TextColumn::make('created_at')->label('ایجاد')->jalaliDateTime(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
