<?php

namespace Haida\FilamentCommerceCore\Filament\Resources;

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
use Haida\FilamentCommerceCore\Filament\Resources\CommerceProductResource\Pages\CreateCommerceProduct;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceProductResource\Pages\EditCommerceProduct;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceProductResource\Pages\ListCommerceProducts;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceProductResource\RelationManagers\CommerceVariantsRelationManager;
use Haida\FilamentCommerceCore\Models\CommerceBrand;
use Haida\FilamentCommerceCore\Models\CommerceCategory;
use Haida\FilamentCommerceCore\Models\CommerceProduct;
use Haida\SiteBuilderCore\Models\Site;

class CommerceProductResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'commerce.catalog';

    protected static ?string $model = CommerceProduct::class;

    protected static ?string $modelLabel = 'محصول';

    protected static ?string $pluralModelLabel = 'محصولات';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string|\UnitEnum|null $navigationGroup = 'فروشگاه';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('site_id')
                    ->label('سایت')
                    ->options(fn () => Site::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('brand_id')
                    ->label('برند')
                    ->options(fn () => CommerceBrand::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
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
                        'simple' => 'ساده',
                        'physical' => 'فیزیکی',
                        'digital' => 'دیجیتال',
                        'service' => 'خدمات',
                        'subscription' => 'اشتراک',
                        'bundle' => 'باندل',
                        'kit' => 'کیت',
                        'gift_card' => 'کارت هدیه',
                    ])
                    ->default('simple')
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش نویس',
                        'active' => 'فعال',
                        'archived' => 'بایگانی',
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
                Toggle::make('track_inventory')
                    ->label('کنترل موجودی')
                    ->default(true),
                Select::make('categories')
                    ->label('دسته بندی')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
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
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('price')->label('قیمت'),
                TextColumn::make('currency')->label('ارز'),
                TextColumn::make('sku')->label('SKU'),
                TextColumn::make('site.name')->label('سایت')->sortable(),
                TextColumn::make('brand.name')->label('برند')->sortable(),
                TextColumn::make('updated_at')->label('بروزرسانی')->jalaliDateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش نویس',
                        'active' => 'فعال',
                        'archived' => 'بایگانی',
                    ]),
                SelectFilter::make('site_id')
                    ->label('سایت')
                    ->options(fn () => Site::query()->pluck('name', 'id')->toArray()),
                SelectFilter::make('brand_id')
                    ->label('برند')
                    ->options(fn () => CommerceBrand::query()->pluck('name', 'id')->toArray()),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            CommerceVariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommerceProducts::route('/'),
            'create' => CreateCommerceProduct::route('/create'),
            'edit' => EditCommerceProduct::route('/{record}/edit'),
        ];
    }
}
