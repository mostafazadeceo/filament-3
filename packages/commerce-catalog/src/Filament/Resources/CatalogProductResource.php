<?php

namespace Haida\CommerceCatalog\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\CommerceCatalog\Filament\Resources\CatalogProductResource\Pages\CreateCatalogProduct;
use Haida\CommerceCatalog\Filament\Resources\CatalogProductResource\Pages\EditCatalogProduct;
use Haida\CommerceCatalog\Filament\Resources\CatalogProductResource\Pages\ListCatalogProducts;
use Haida\CommerceCatalog\Filament\Resources\CatalogProductResource\RelationManagers\CatalogMediaRelationManager;
use Haida\CommerceCatalog\Filament\Resources\CatalogProductResource\RelationManagers\CatalogVariantsRelationManager;
use Haida\CommerceCatalog\Models\CatalogCollection;
use Haida\CommerceCatalog\Models\CatalogProduct;
use Haida\SiteBuilderCore\Models\Site;
use Vendor\FilamentAccountingIr\Models\InventoryItem;
use Vendor\FilamentAccountingIr\Models\ProductService;

class CatalogProductResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'catalog.product';

    protected static ?string $model = CatalogProduct::class;

    protected static ?string $modelLabel = 'محصول';

    protected static ?string $pluralModelLabel = 'محصولات';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string|\UnitEnum|null $navigationGroup = 'کاتالوگ فروش';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('site_id')
                    ->label('سایت')
                    ->options(fn () => Site::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('اسلاگ')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('نوع')
                    ->options([
                        'physical' => 'فیزیکی',
                        'digital_code' => 'کد دیجیتال',
                        'downloadable' => 'دانلودی',
                        'service' => 'خدمات',
                        'subscription' => 'اشتراک',
                        'bundle' => 'باندل',
                        'gift_card' => 'کارت هدیه',
                    ])
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش نویس',
                        'published' => 'منتشر شده',
                    ])
                    ->default('draft')
                    ->required(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->maxLength(120),
                Textarea::make('summary')
                    ->label('خلاصه')
                    ->rows(3)
                    ->nullable(),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->rows(6)
                    ->nullable(),
                TextInput::make('currency')
                    ->label('ارز')
                    ->default(fn () => config('commerce-catalog.defaults.currency', 'IRR'))
                    ->maxLength(8),
                TextInput::make('price')
                    ->label('قیمت')
                    ->numeric()
                    ->required(),
                TextInput::make('compare_at_price')
                    ->label('قیمت قبل')
                    ->numeric()
                    ->nullable(),
                Toggle::make('track_inventory')
                    ->label('کنترل موجودی')
                    ->default(true),
                Select::make('accounting_product_id')
                    ->label('کالای حسابداری')
                    ->options(fn () => ProductService::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('inventory_item_id')
                    ->label('آیتم موجودی')
                    ->options(fn () => InventoryItem::query()
                        ->with('product')
                        ->get()
                        ->mapWithKeys(fn (InventoryItem $item) => [
                            $item->getKey() => trim(($item->sku ?: '') . ' ' . ($item->product?->name ?: '')),
                        ])
                        ->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('collections')
                    ->label('مجموعه ها')
                    ->relationship('collections', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('slug')->label('اسلاگ')->searchable(),
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('price')->label('قیمت'),
                TextColumn::make('currency')->label('ارز'),
                TextColumn::make('published_at')->label('انتشار')->jalaliDate(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش نویس',
                        'published' => 'منتشر شده',
                    ]),
                SelectFilter::make('site_id')
                    ->label('سایت')
                    ->options(fn () => Site::query()->pluck('name', 'id')->toArray()),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            CatalogVariantsRelationManager::class,
            CatalogMediaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCatalogProducts::route('/'),
            'create' => CreateCatalogProduct::route('/create'),
            'edit' => EditCatalogProduct::route('/{record}/edit'),
        ];
    }
}
