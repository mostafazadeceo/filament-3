<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantPurchaseRequestResource\Pages\CreateRestaurantPurchaseRequest;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantPurchaseRequestResource\Pages\EditRestaurantPurchaseRequest;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantPurchaseRequestResource\Pages\ListRestaurantPurchaseRequests;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantPurchaseRequestResource\RelationManagers\PurchaseRequestLinesRelationManager;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseRequest;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class RestaurantPurchaseRequestResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'restaurant.purchase_request';

    protected static ?string $model = RestaurantPurchaseRequest::class;

    protected static ?string $modelLabel = 'درخواست خرید';

    protected static ?string $pluralModelLabel = 'درخواست‌های خرید';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-plus';

    protected static string|\UnitEnum|null $navigationGroup = 'خرید';

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
                Select::make('requested_by')
                    ->label('درخواست‌دهنده')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'pending' => 'در انتظار تایید',
                        'approved' => 'تایید شده',
                        'rejected' => 'رد شده',
                    ])
                    ->default('draft'),
                DatePicker::make('needed_at')
                    ->label('تاریخ نیاز')
                    ->nullable(),
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
                TextColumn::make('id')->label('شناسه')->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('needed_at')->label('تاریخ نیاز')->jalaliDate(),
                TextColumn::make('created_at')->label('ایجاد')->jalaliDateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PurchaseRequestLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRestaurantPurchaseRequests::route('/'),
            'create' => CreateRestaurantPurchaseRequest::route('/create'),
            'edit' => EditRestaurantPurchaseRequest::route('/{record}/edit'),
        ];
    }
}
