<?php

namespace Haida\FilamentCommerceCore\Filament\Resources\CommercePriceListResource\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCommerceCore\Models\CommerceProduct;
use Haida\FilamentCommerceCore\Models\CommerceVariant;

class CommercePricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('product_id')
                    ->label('محصول')
                    ->options(fn () => CommerceProduct::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('variant_id')
                    ->label('واریانت')
                    ->options(fn () => CommerceVariant::query()
                        ->get()
                        ->mapWithKeys(fn (CommerceVariant $variant) => [
                            $variant->getKey() => trim(($variant->sku ?: '').' '.($variant->name ?: '')),
                        ])
                        ->toArray())
                    ->searchable()
                    ->nullable(),
                TextInput::make('currency')
                    ->label('ارز')
                    ->default(fn () => config('filament-commerce-core.defaults.currency', 'IRR'))
                    ->maxLength(8),
                TextInput::make('price')
                    ->label('قیمت')
                    ->numeric()
                    ->required(),
                TextInput::make('compare_at_price')
                    ->label('قیمت قبل')
                    ->numeric()
                    ->nullable(),
                DateTimePicker::make('starts_at')
                    ->label('شروع'),
                DateTimePicker::make('ends_at')
                    ->label('پایان'),
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
                TextColumn::make('product.name')->label('محصول'),
                TextColumn::make('variant.name')->label('واریانت'),
                TextColumn::make('price')->label('قیمت'),
                TextColumn::make('currency')->label('ارز'),
                TextColumn::make('starts_at')->label('شروع')->jalaliDateTime(),
                TextColumn::make('ends_at')->label('پایان')->jalaliDateTime(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = $data['tenant_id'] ?? $this->getOwnerRecord()->tenant_id;

        return $data;
    }
}
