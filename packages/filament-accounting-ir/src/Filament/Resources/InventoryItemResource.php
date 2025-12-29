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
use Vendor\FilamentAccountingIr\Filament\Resources\InventoryItemResource\Pages\CreateInventoryItem;
use Vendor\FilamentAccountingIr\Filament\Resources\InventoryItemResource\Pages\EditInventoryItem;
use Vendor\FilamentAccountingIr\Filament\Resources\InventoryItemResource\Pages\ListInventoryItems;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\InventoryItem;
use Vendor\FilamentAccountingIr\Models\ProductService;

class InventoryItemResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = InventoryItem::class;

    protected static ?string $modelLabel = 'کالای انبار';

    protected static ?string $pluralModelLabel = 'کالاهای انبار';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'کالاهای انبار';

    protected static string|\UnitEnum|null $navigationGroup = 'انبار';

    protected static ?int $navigationSort = 2;

    protected static array $eagerLoad = ['company', 'product'];

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
                Select::make('product_id')
                    ->label('کالا/خدمت')
                    ->options(fn () => ProductService::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->maxLength(64),
                TextInput::make('min_stock')
                    ->label('حداقل موجودی')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('current_stock')
                    ->label('موجودی فعلی')
                    ->numeric()
                    ->minValue(0),
                Toggle::make('allow_negative')
                    ->label('اجازه منفی')
                    ->default(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->label('کالا/خدمت')->sortable(),
                TextColumn::make('sku')->label('SKU')->searchable(),
                TextColumn::make('current_stock')->label('موجودی')->numeric(decimalPlaces: 2),
                ToggleColumn::make('allow_negative')->label('منفی'),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventoryItems::route('/'),
            'create' => CreateInventoryItem::route('/create'),
            'edit' => EditInventoryItem::route('/{record}/edit'),
        ];
    }
}
