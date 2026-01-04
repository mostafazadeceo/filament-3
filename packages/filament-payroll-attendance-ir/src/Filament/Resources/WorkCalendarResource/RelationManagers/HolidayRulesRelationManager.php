<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\WorkCalendarResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HolidayRulesRelationManager extends RelationManager
{
    protected static string $relationship = 'holidayRules';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('holiday_date')
                    ->label('تاریخ تعطیل')
                    ->required(),
                TextInput::make('title')
                    ->label('عنوان')
                    ->required(),
                Toggle::make('is_public')
                    ->label('عمومی')
                    ->default(true),
                Select::make('source')
                    ->label('منبع')
                    ->options([
                        'manual' => 'دستی',
                        'import' => 'ورودی',
                        'system' => 'سیستم',
                    ])
                    ->default('manual')
                    ->required(),
                KeyValue::make('metadata')
                    ->label('متادیتا')
                    ->keyLabel('کلید')
                    ->valueLabel('مقدار')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('holiday_date')->label('تاریخ')->jalaliDate(),
                TextColumn::make('title')->label('عنوان')->searchable(),
                TextColumn::make('source')->label('منبع'),
                IconColumn::make('is_public')->label('عمومی')->boolean(),
            ])
            ->defaultSort('holiday_date', 'desc');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = $this->getOwnerRecord()->company_id;

        return $data;
    }
}
