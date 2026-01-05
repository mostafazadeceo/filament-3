<?php

namespace Haida\CommerceCatalog\Filament\Resources\CatalogProductResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\InventoryItem;

class CatalogVariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

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
                    ->maxLength(120),
                TextInput::make('currency')
                    ->label('ارز')
                    ->default(fn () => config('commerce-catalog.defaults.currency', 'IRR'))
                    ->maxLength(8),
                TextInput::make('price')
                    ->label('قیمت')
                    ->numeric()
                    ->required(),
                Toggle::make('is_default')
                    ->label('پیش فرض')
                    ->default(false),
                Select::make('inventory_item_id')
                    ->label('آیتم موجودی')
                    ->options(fn () => InventoryItem::query()
                        ->with('product')
                        ->get()
                        ->mapWithKeys(fn (InventoryItem $item) => [
                            $item->getKey() => trim(($item->sku ?: '').' '.($item->product?->name ?: '')),
                        ])
                        ->toArray())
                    ->searchable()
                    ->nullable(),
                Textarea::make('attributes')
                    ->label('ویژگی ها (JSON)')
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
                TextColumn::make('name')->label('نام'),
                TextColumn::make('sku')->label('SKU'),
                TextColumn::make('price')->label('قیمت'),
                TextColumn::make('currency')->label('ارز'),
                IconColumn::make('is_default')->label('پیش فرض')->boolean(),
            ]);
    }
}
