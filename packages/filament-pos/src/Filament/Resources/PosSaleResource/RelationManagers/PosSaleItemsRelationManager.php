<?php

namespace Haida\FilamentPos\Filament\Resources\PosSaleResource\RelationManagers;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PosSaleItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sku')
                    ->label('SKU')
                    ->maxLength(120)
                    ->nullable(),
                TextInput::make('barcode')
                    ->label('بارکد')
                    ->maxLength(120)
                    ->nullable(),
                TextInput::make('quantity')
                    ->label('تعداد')
                    ->numeric()
                    ->default(1),
                TextInput::make('unit_price')
                    ->label('قیمت واحد')
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_amount')
                    ->label('تخفیف')
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_amount')
                    ->label('مالیات')
                    ->numeric()
                    ->default(0),
                TextInput::make('total')
                    ->label('جمع')
                    ->numeric()
                    ->default(0),
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
                TextColumn::make('name')
                    ->label('نام'),
                TextColumn::make('quantity')
                    ->label('تعداد'),
                TextColumn::make('unit_price')
                    ->label('قیمت واحد'),
                TextColumn::make('total')
                    ->label('جمع'),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = $data['tenant_id'] ?? $this->getOwnerRecord()->tenant_id;

        return $data;
    }
}
