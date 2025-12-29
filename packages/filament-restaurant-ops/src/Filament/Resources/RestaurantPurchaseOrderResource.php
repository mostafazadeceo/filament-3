<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentRestaurantOps\Filament\Resources\Concerns\HasEagerLoads;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantPurchaseOrderResource\Pages\CreateRestaurantPurchaseOrder;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantPurchaseOrderResource\Pages\EditRestaurantPurchaseOrder;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantPurchaseOrderResource\Pages\ListRestaurantPurchaseOrders;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantPurchaseOrderResource\RelationManagers\PurchaseOrderLinesRelationManager;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseOrder;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseRequest;
use Haida\FilamentRestaurantOps\Models\RestaurantSupplier;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class RestaurantPurchaseOrderResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'restaurant.purchase_order';

    protected static ?string $model = RestaurantPurchaseOrder::class;

    protected static ?string $modelLabel = 'سفارش خرید';

    protected static ?string $pluralModelLabel = 'سفارش‌های خرید';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'خرید';

    protected static array $eagerLoad = ['supplier'];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(fn () => AccountingCompany::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('branch_id')
                    ->label('شعبه')
                    ->options(fn () => AccountingBranch::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('supplier_id')
                    ->label('تأمین‌کننده')
                    ->options(fn () => RestaurantSupplier::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('purchase_request_id')
                    ->label('درخواست خرید')
                    ->options(fn () => RestaurantPurchaseRequest::query()->pluck('id', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                TextInput::make('order_no')
                    ->label('شماره سفارش')
                    ->maxLength(64),
                DatePicker::make('order_date')
                    ->label('تاریخ سفارش')
                    ->nullable(),
                DatePicker::make('expected_at')
                    ->label('تاریخ تحویل')
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'sent' => 'ارسال شده',
                        'partially_received' => 'دریافت ناقص',
                        'received' => 'دریافت کامل',
                        'cancelled' => 'لغو شده',
                    ])
                    ->default('draft'),
                TextInput::make('subtotal')
                    ->label('جمع جزء')
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_total')
                    ->label('مالیات')
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_total')
                    ->label('تخفیف')
                    ->numeric()
                    ->default(0),
                TextInput::make('total')
                    ->label('جمع کل')
                    ->numeric()
                    ->default(0),
                Textarea::make('notes')
                    ->label('توضیحات')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_no')->label('شماره')->searchable()->sortable(),
                TextColumn::make('supplier.name')->label('تأمین‌کننده')->searchable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('order_date')->label('تاریخ سفارش')->jalaliDate(),
                TextColumn::make('total')->label('جمع کل'),
            ])
            ->defaultSort('order_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PurchaseOrderLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRestaurantPurchaseOrders::route('/'),
            'create' => CreateRestaurantPurchaseOrder::route('/create'),
            'edit' => EditRestaurantPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
