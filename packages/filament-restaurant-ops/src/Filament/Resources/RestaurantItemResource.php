<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentRestaurantOps\Filament\Resources\Concerns\HasEagerLoads;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantItemResource\Pages\CreateRestaurantItem;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantItemResource\Pages\EditRestaurantItem;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantItemResource\Pages\ListRestaurantItems;
use Haida\FilamentRestaurantOps\Models\RestaurantItem;
use Haida\FilamentRestaurantOps\Models\RestaurantUom;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\InventoryItem;

class RestaurantItemResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'restaurant.item';

    protected static ?string $model = RestaurantItem::class;

    protected static ?string $modelLabel = 'کالا';

    protected static ?string $pluralModelLabel = 'کالاها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static string|\UnitEnum|null $navigationGroup = 'اطلاعات پایه';

    protected static array $eagerLoad = ['baseUom', 'accountingItem'];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(fn () => AccountingCompany::query()->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->searchable()
                    ->required(),
                Select::make('accounting_inventory_item_id')
                    ->label('کالای حسابداری')
                    ->options(fn (callable $get) => InventoryItem::query()
                        ->where('company_id', $get('company_id'))
                        ->pluck('name', 'id')
                        ->toArray())
                    ->searchable()
                    ->nullable(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('کد')
                    ->maxLength(64),
                TextInput::make('category')
                    ->label('دسته')
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
                Select::make('base_uom_id')
                    ->label('واحد پایه')
                    ->options(fn () => RestaurantUom::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                Select::make('purchase_uom_id')
                    ->label('واحد خرید')
                    ->options(fn () => RestaurantUom::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                Select::make('consumption_uom_id')
                    ->label('واحد مصرف')
                    ->options(fn () => RestaurantUom::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                TextInput::make('purchase_to_base_rate')
                    ->label('ضریب تبدیل خرید به پایه')
                    ->numeric()
                    ->default(1),
                TextInput::make('consumption_to_base_rate')
                    ->label('ضریب تبدیل مصرف به پایه')
                    ->numeric()
                    ->default(1),
                TextInput::make('min_stock')
                    ->label('حداقل موجودی')
                    ->numeric(),
                TextInput::make('max_stock')
                    ->label('حداکثر موجودی')
                    ->numeric(),
                TextInput::make('reorder_point')
                    ->label('نقطه سفارش')
                    ->numeric(),
                Toggle::make('track_batch')
                    ->label('رهگیری بچ')
                    ->default(false),
                Toggle::make('track_expiry')
                    ->label('رهگیری انقضا')
                    ->default(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('category')->label('دسته')->searchable(),
                TextColumn::make('baseUom.name')->label('واحد پایه'),
                TextColumn::make('accountingItem.name')->label('کالای حسابداری'),
                TextColumn::make('reorder_point')->label('نقطه سفارش'),
                IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRestaurantItems::route('/'),
            'create' => CreateRestaurantItem::route('/create'),
            'edit' => EditRestaurantItem::route('/{record}/edit'),
        ];
    }
}
