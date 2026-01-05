<?php

namespace Haida\FilamentCommerceCore\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceInventoryItemResource\Pages\CreateCommerceInventoryItem;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceInventoryItemResource\Pages\EditCommerceInventoryItem;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceInventoryItemResource\Pages\ListCommerceInventoryItems;
use Haida\FilamentCommerceCore\Models\CommerceInventoryItem;
use Haida\FilamentCommerceCore\Models\CommerceProduct;
use Haida\FilamentCommerceCore\Models\CommerceVariant;

class CommerceInventoryItemResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'commerce.inventory';

    protected static ?string $model = CommerceInventoryItem::class;

    protected static ?string $modelLabel = 'آیتم موجودی';

    protected static ?string $pluralModelLabel = 'آیتم های موجودی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static string|\UnitEnum|null $navigationGroup = 'فروشگاه';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
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
                TextInput::make('sku')
                    ->label('SKU')
                    ->maxLength(120),
                TextInput::make('location_label')
                    ->label('موقعیت')
                    ->maxLength(255),
                TextInput::make('quantity_on_hand')
                    ->label('موجودی')
                    ->numeric()
                    ->default(0),
                TextInput::make('quantity_reserved')
                    ->label('رزرو شده')
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('active')
                    ->required(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->label('محصول')->sortable(),
                TextColumn::make('variant.name')->label('واریانت'),
                TextColumn::make('sku')->label('SKU'),
                TextColumn::make('quantity_on_hand')->label('موجودی'),
                TextColumn::make('quantity_reserved')->label('رزرو'),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommerceInventoryItems::route('/'),
            'create' => CreateCommerceInventoryItem::route('/create'),
            'edit' => EditCommerceInventoryItem::route('/{record}/edit'),
        ];
    }
}
