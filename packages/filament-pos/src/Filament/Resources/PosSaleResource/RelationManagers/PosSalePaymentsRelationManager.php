<?php

namespace Haida\FilamentPos\Filament\Resources\PosSaleResource\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PosSalePaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('provider')
                    ->label('درگاه')
                    ->options([
                        'manual' => 'ترمینال/دستی',
                        'cash' => 'نقدی',
                        'card' => 'کارت',
                    ])
                    ->default('manual')
                    ->required(),
                TextInput::make('amount')
                    ->label('مبلغ')
                    ->numeric()
                    ->required(),
                TextInput::make('currency')
                    ->label('ارز')
                    ->default(fn () => config('filament-pos.defaults.currency', 'IRR'))
                    ->maxLength(8),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'confirmed' => 'تایید شده',
                        'failed' => 'ناموفق',
                    ])
                    ->default('pending')
                    ->required(),
                TextInput::make('reference')
                    ->label('شناسه')
                    ->maxLength(255)
                    ->nullable(),
                DateTimePicker::make('processed_at')
                    ->label('پردازش')
                    ->nullable(),
                Textarea::make('metadata')
                    ->label('متادیتا (JSON)')
                    ->rows(3)
                    ->nullable()
                    ->rules(['nullable', 'json'])
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }

                        return $state;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (! is_string($state) || trim($state) === '') {
                            return null;
                        }

                        $decoded = json_decode($state, true);

                        return is_array($decoded) ? $decoded : null;
                    }),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('provider')
                    ->label('درگاه'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('amount')
                    ->label('مبلغ'),
                TextColumn::make('currency')
                    ->label('ارز'),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = $data['tenant_id'] ?? $this->getOwnerRecord()->tenant_id;

        return $data;
    }
}
