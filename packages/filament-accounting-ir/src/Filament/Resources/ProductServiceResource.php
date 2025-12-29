<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\ProductServiceResource\Pages\CreateProductService;
use Vendor\FilamentAccountingIr\Filament\Resources\ProductServiceResource\Pages\EditProductService;
use Vendor\FilamentAccountingIr\Filament\Resources\ProductServiceResource\Pages\ListProductServices;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\ProductService;
use Vendor\FilamentAccountingIr\Models\TaxCategory;
use Vendor\FilamentAccountingIr\Models\Uom;

class ProductServiceResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = ProductService::class;

    protected static ?string $modelLabel = 'کالا/خدمت';

    protected static ?string $pluralModelLabel = 'کالاها و خدمات';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'کالاها و خدمات';

    protected static string|\UnitEnum|null $navigationGroup = 'اطلاعات پایه';

    protected static ?int $navigationSort = 2;

    protected static array $eagerLoad = ['company', 'taxCategory', 'uom'];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(fn () => AccountingCompany::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('code')
                    ->label('کد')
                    ->maxLength(64),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                Select::make('item_type')
                    ->label('نوع')
                    ->options([
                        'product' => 'کالا',
                        'service' => 'خدمت',
                    ])
                    ->default('product'),
                Select::make('uom_id')
                    ->label('واحد')
                    ->options(fn () => Uom::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                Select::make('tax_category_id')
                    ->label('دسته مالیاتی')
                    ->options(fn () => TaxCategory::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                TextInput::make('base_price')
                    ->label('قیمت پایه')
                    ->numeric()
                    ->minValue(0),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('کد')->searchable(),
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('item_type')->label('نوع')->badge(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
                ToggleColumn::make('is_active')->label('فعال'),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductServices::route('/'),
            'create' => CreateProductService::route('/create'),
            'edit' => EditProductService::route('/{record}/edit'),
        ];
    }
}
