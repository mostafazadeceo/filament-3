<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action as TableAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentRestaurantOps\Filament\Resources\Concerns\HasEagerLoads;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantMenuSaleResource\Pages\CreateRestaurantMenuSale;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantMenuSaleResource\Pages\EditRestaurantMenuSale;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantMenuSaleResource\Pages\ListRestaurantMenuSales;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantMenuSaleResource\RelationManagers\MenuSaleLinesRelationManager;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuSale;
use Haida\FilamentRestaurantOps\Models\RestaurantWarehouse;
use Haida\FilamentRestaurantOps\Services\RestaurantMenuSaleService;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class RestaurantMenuSaleResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'restaurant.menu_sale';

    protected static ?string $model = RestaurantMenuSale::class;

    protected static ?string $modelLabel = 'فروش منو';

    protected static ?string $pluralModelLabel = 'فروش‌های منو';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string|\UnitEnum|null $navigationGroup = 'کاست‌کنترل';

    protected static array $eagerLoad = ['company', 'branch', 'warehouse'];

    public static function canEdit($record): bool
    {
        return $record instanceof RestaurantMenuSale
            && $record->status !== 'posted'
            && auth()->user()?->can('update', $record);
    }

    public static function canDelete($record): bool
    {
        return $record instanceof RestaurantMenuSale
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
                    ->label('انبار مصرف')
                    ->options(fn () => RestaurantWarehouse::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                DatePicker::make('sale_date')
                    ->label('تاریخ فروش')
                    ->required(),
                Select::make('source')
                    ->label('منبع')
                    ->options([
                        'manual' => 'دستی',
                        'pos' => 'POS',
                        'import' => 'ایمپورت',
                    ])
                    ->default('manual'),
                TextInput::make('external_ref')
                    ->label('شناسه بیرونی')
                    ->maxLength(255),
                TextInput::make('total_amount')
                    ->label('مبلغ کل')
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'posted' => 'قطعی',
                    ])
                    ->default('draft')
                    ->disabled()
                    ->dehydrated(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sale_date')->label('تاریخ')->jalaliDate()->sortable(),
                TextColumn::make('branch.name')->label('شعبه'),
                TextColumn::make('warehouse.name')->label('انبار'),
                TextColumn::make('source')->label('منبع')->badge(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('total_amount')->label('مبلغ کل'),
            ])
            ->actions([
                TableAction::make('post')
                    ->label('ثبت قطعی')
                    ->visible(fn (RestaurantMenuSale $record) => $record->status !== 'posted' && auth()->user()?->can('post', $record))
                    ->requiresConfirmation()
                    ->action(function (RestaurantMenuSale $record): void {
                        app(RestaurantMenuSaleService::class)->post($record);
                        Notification::make()->title('فروش منو قطعی شد.')->success()->send();
                    }),
            ])
            ->defaultSort('sale_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            MenuSaleLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRestaurantMenuSales::route('/'),
            'create' => CreateRestaurantMenuSale::route('/create'),
            'edit' => EditRestaurantMenuSale::route('/{record}/edit'),
        ];
    }
}
