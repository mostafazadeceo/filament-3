<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\InventoryDocResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\InventoryDoc;
use Vendor\FilamentAccountingIr\Models\InventoryItem;
use Vendor\FilamentAccountingIr\Models\InventoryLocation;

class InventoryDocLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'اقلام سند';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inventory_item_id')
                    ->label('کالا')
                    ->options(fn () => InventoryItem::query()
                        ->with('product')
                        ->get()
                        ->mapWithKeys(function (InventoryItem $item) {
                            $label = $item->product?->name ?? $item->sku ?? (string) $item->getKey();

                            return [$item->getKey() => $label];
                        })
                        ->toArray())
                    ->searchable()
                    ->required(),
                Select::make('location_id')
                    ->label('موقعیت')
                    ->options(fn () => InventoryLocation::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                TextInput::make('quantity')
                    ->label('مقدار')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('unit_cost')
                    ->label('بهای واحد')
                    ->numeric()
                    ->minValue(0),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.product.name')->label('کالا')->sortable(),
                TextColumn::make('quantity')->label('مقدار')->numeric(decimalPlaces: 2),
                TextColumn::make('unit_cost')->label('بهای واحد')->numeric(decimalPlaces: 0),
                TextColumn::make('location.name')->label('موقعیت')->sortable(),
            ]);
    }

    public function canCreate(): bool
    {
        $owner = $this->getOwnerRecord();

        return $owner instanceof InventoryDoc
            && $owner->status !== 'posted'
            && parent::canCreate();
    }

    public function canEdit($record): bool
    {
        $owner = $this->getOwnerRecord();

        return $owner instanceof InventoryDoc
            && $owner->status !== 'posted'
            && parent::canEdit($record);
    }

    public function canDelete($record): bool
    {
        $owner = $this->getOwnerRecord();

        return $owner instanceof InventoryDoc
            && $owner->status !== 'posted'
            && parent::canDelete($record);
    }
}
