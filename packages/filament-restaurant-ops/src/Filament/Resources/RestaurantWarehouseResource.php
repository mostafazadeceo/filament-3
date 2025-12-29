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
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantWarehouseResource\Pages\CreateRestaurantWarehouse;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantWarehouseResource\Pages\EditRestaurantWarehouse;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantWarehouseResource\Pages\ListRestaurantWarehouses;
use Haida\FilamentRestaurantOps\Models\RestaurantWarehouse;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\InventoryWarehouse;

class RestaurantWarehouseResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'restaurant.warehouse';

    protected static ?string $model = RestaurantWarehouse::class;

    protected static ?string $modelLabel = 'انبار';

    protected static ?string $pluralModelLabel = 'انبارها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

    protected static string|\UnitEnum|null $navigationGroup = 'انبار';

    protected static array $eagerLoad = ['accountingWarehouse'];

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
                Select::make('branch_id')
                    ->label('شعبه')
                    ->options(fn () => AccountingBranch::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('accounting_inventory_warehouse_id')
                    ->label('انبار حسابداری')
                    ->options(fn (callable $get) => InventoryWarehouse::query()
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
                Select::make('type')
                    ->label('نوع')
                    ->options([
                        'main' => 'اصلی',
                        'cold' => 'سردخانه',
                        'dry' => 'خشکبار',
                    ])
                    ->default('main'),
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
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('code')->label('کد')->searchable(),
                TextColumn::make('type')->label('نوع')->badge(),
                TextColumn::make('accountingWarehouse.name')->label('انبار حسابداری'),
                IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRestaurantWarehouses::route('/'),
            'create' => CreateRestaurantWarehouse::route('/create'),
            'edit' => EditRestaurantWarehouse::route('/{record}/edit'),
        ];
    }
}
