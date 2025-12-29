<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action as TableAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentRestaurantOps\Filament\Resources\Concerns\HasEagerLoads;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantGoodsReceiptResource\Pages\CreateRestaurantGoodsReceipt;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantGoodsReceiptResource\Pages\EditRestaurantGoodsReceipt;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantGoodsReceiptResource\Pages\ListRestaurantGoodsReceipts;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantGoodsReceiptResource\RelationManagers\GoodsReceiptLinesRelationManager;
use Haida\FilamentRestaurantOps\Models\RestaurantGoodsReceipt;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseOrder;
use Haida\FilamentRestaurantOps\Models\RestaurantSupplier;
use Haida\FilamentRestaurantOps\Models\RestaurantWarehouse;
use Haida\FilamentRestaurantOps\Services\RestaurantGoodsReceiptService;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class RestaurantGoodsReceiptResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'restaurant.goods_receipt';

    protected static ?string $model = RestaurantGoodsReceipt::class;

    protected static ?string $modelLabel = 'رسید کالا';

    protected static ?string $pluralModelLabel = 'رسیدهای کالا';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static string|\UnitEnum|null $navigationGroup = 'خرید';

    protected static array $eagerLoad = ['supplier', 'warehouse'];

    public static function canEdit($record): bool
    {
        return $record instanceof RestaurantGoodsReceipt
            && $record->status !== 'posted'
            && auth()->user()?->can('update', $record);
    }

    public static function canDelete($record): bool
    {
        return $record instanceof RestaurantGoodsReceipt
            && $record->status !== 'posted'
            && auth()->user()?->can('delete', $record);
    }

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
                Select::make('warehouse_id')
                    ->label('انبار')
                    ->options(fn () => RestaurantWarehouse::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('supplier_id')
                    ->label('تأمین‌کننده')
                    ->options(fn () => RestaurantSupplier::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('purchase_order_id')
                    ->label('سفارش خرید')
                    ->options(fn () => RestaurantPurchaseOrder::query()->pluck('order_no', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                TextInput::make('receipt_no')
                    ->label('شماره رسید')
                    ->maxLength(64),
                DatePicker::make('receipt_date')
                    ->label('تاریخ رسید')
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'posted' => 'ثبت شده',
                    ])
                    ->default('draft')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('subtotal')
                    ->label('جمع جزء')
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_total')
                    ->label('مالیات')
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
                TextColumn::make('receipt_no')->label('شماره')->searchable()->sortable(),
                TextColumn::make('warehouse.name')->label('انبار'),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('receipt_date')->label('تاریخ رسید')->jalaliDate(),
                TextColumn::make('total')->label('جمع کل'),
            ])
            ->actions([
                TableAction::make('post')
                    ->label('ثبت قطعی')
                    ->visible(fn (RestaurantGoodsReceipt $record) => $record->status !== 'posted' && auth()->user()?->can('post', $record))
                    ->requiresConfirmation()
                    ->action(function (RestaurantGoodsReceipt $record): void {
                        app(RestaurantGoodsReceiptService::class)->post($record);
                        Notification::make()->title('رسید کالا قطعی شد.')->success()->send();
                    }),
            ])
            ->defaultSort('receipt_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            GoodsReceiptLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRestaurantGoodsReceipts::route('/'),
            'create' => CreateRestaurantGoodsReceipt::route('/create'),
            'edit' => EditRestaurantGoodsReceipt::route('/{record}/edit'),
        ];
    }
}
