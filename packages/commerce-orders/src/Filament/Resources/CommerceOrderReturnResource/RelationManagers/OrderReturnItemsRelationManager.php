<?php

namespace Haida\CommerceOrders\Filament\Resources\CommerceOrderReturnResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\CommerceOrders\Models\OrderItem;

class OrderReturnItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('order_item_id')
                    ->label('آیتم سفارش')
                    ->options(fn () => OrderItem::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sku')
                    ->label('SKU')
                    ->maxLength(120),
                TextInput::make('quantity')
                    ->label('تعداد')
                    ->numeric()
                    ->required(),
                TextInput::make('reason')
                    ->label('دلیل')
                    ->maxLength(255)
                    ->nullable(),
                TextInput::make('status')
                    ->label('وضعیت')
                    ->default('requested')
                    ->required(),
                Textarea::make('meta')
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
                TextColumn::make('quantity')->label('تعداد'),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = $data['tenant_id'] ?? $this->getOwnerRecord()->tenant_id;

        return $data;
    }
}
