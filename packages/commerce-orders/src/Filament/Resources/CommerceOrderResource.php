<?php

namespace Haida\CommerceOrders\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderResource\Pages\EditCommerceOrder;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderResource\Pages\ListCommerceOrders;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderResource\Pages\ViewCommerceOrder;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderResource\RelationManagers\OrderItemsRelationManager;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderResource\RelationManagers\OrderPaymentsRelationManager;
use Haida\CommerceOrders\Models\Order;
use Haida\FeatureGates\Services\FeatureGateService;
use Haida\SiteBuilderCore\Models\Site;

class CommerceOrderResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'commerce.order';

    protected static ?string $model = Order::class;

    protected static ?string $modelLabel = 'سفارش';

    protected static ?string $pluralModelLabel = 'سفارش‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string|\UnitEnum|null $navigationGroup = 'فروشگاه';

    public static function shouldRegisterNavigation(): bool
    {
        if (! class_exists(FeatureGateService::class)) {
            return true;
        }

        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            return true;
        }

        $decision = app(FeatureGateService::class)->evaluate($tenant, 'commerce.order.view');

        return $decision->allowed;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('status')
                    ->label('وضعیت سفارش')
                    ->options([
                        'pending' => 'در انتظار',
                        'processing' => 'در حال پردازش',
                        'fulfilled' => 'تکمیل شده',
                        'completed' => 'نهایی شده',
                        'cancelled' => 'لغو شده',
                        'refunded' => 'مرجوع شده',
                    ])
                    ->required(),
                Select::make('payment_status')
                    ->label('وضعیت پرداخت')
                    ->options([
                        'pending' => 'در انتظار',
                        'paid' => 'پرداخت شده',
                        'failed' => 'ناموفق',
                        'refunded' => 'مرجوع شده',
                        'partially_refunded' => 'مرجوعی جزئی',
                    ])
                    ->required(),
                Textarea::make('internal_note')
                    ->label('یادداشت داخلی')
                    ->rows(4)
                    ->nullable(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->label('شماره سفارش')->searchable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('payment_status')->label('پرداخت')->badge(),
                TextColumn::make('total')->label('مبلغ کل')->sortable(),
                TextColumn::make('currency')->label('ارز'),
                TextColumn::make('customer_name')->label('مشتری')->searchable(),
                TextColumn::make('site.name')->label('سایت')->sortable(),
                TextColumn::make('placed_at')->label('ثبت')->jalaliDateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت سفارش')
                    ->options([
                        'pending' => 'در انتظار',
                        'processing' => 'در حال پردازش',
                        'fulfilled' => 'تکمیل شده',
                        'completed' => 'نهایی شده',
                        'cancelled' => 'لغو شده',
                        'refunded' => 'مرجوع شده',
                    ]),
                SelectFilter::make('payment_status')
                    ->label('وضعیت پرداخت')
                    ->options([
                        'pending' => 'در انتظار',
                        'paid' => 'پرداخت شده',
                        'failed' => 'ناموفق',
                        'refunded' => 'مرجوع شده',
                        'partially_refunded' => 'مرجوعی جزئی',
                    ]),
                SelectFilter::make('site_id')
                    ->label('سایت')
                    ->options(fn () => Site::query()->pluck('name', 'id')->toArray()),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('جزئیات سفارش')
                    ->schema([
                        TextEntry::make('number')->label('شماره سفارش'),
                        TextEntry::make('status')->label('وضعیت'),
                        TextEntry::make('payment_status')->label('وضعیت پرداخت'),
                        TextEntry::make('total')->label('مبلغ کل'),
                        TextEntry::make('currency')->label('ارز'),
                        TextEntry::make('customer_name')->label('نام مشتری'),
                        TextEntry::make('customer_email')->label('ایمیل'),
                        TextEntry::make('customer_phone')->label('تلفن'),
                        TextEntry::make('placed_at')->label('زمان ثبت')->jalaliDateTime(),
                        TextEntry::make('paid_at')->label('زمان پرداخت')->jalaliDateTime(),
                    ])
                    ->columns(2),
                Section::make('آدرس‌ها')
                    ->schema([
                        TextEntry::make('billing_address')
                            ->label('آدرس صورتحساب')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '-'),
                        TextEntry::make('shipping_address')
                            ->label('آدرس ارسال')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '-'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrderItemsRelationManager::class,
            OrderPaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommerceOrders::route('/'),
            'view' => ViewCommerceOrder::route('/{record}'),
            'edit' => EditCommerceOrder::route('/{record}/edit'),
        ];
    }
}
