<?php

namespace Haida\FilamentRelograde\Resources;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\FilamentRelograde\Models\RelogradeAccount;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Models\RelogradeOrder;
use Haida\FilamentRelograde\Models\RelogradeProduct;
use Haida\FilamentRelograde\Resources\RelogradeOrderResource\Pages\CreateRelogradeOrder;
use Haida\FilamentRelograde\Resources\RelogradeOrderResource\Pages\ListRelogradeOrders;
use Haida\FilamentRelograde\Resources\RelogradeOrderResource\Pages\ViewRelogradeOrder;
use Haida\FilamentRelograde\Services\RelogradeOrderService;
use Haida\FilamentRelograde\Support\RelogradeAuthorization;
use Haida\FilamentRelograde\Support\RelogradeCurrencyFormatter;
use Haida\FilamentRelograde\Support\RelogradeLabels;
use Haida\FilamentRelograde\Support\RelogradeNotifier;

class RelogradeOrderResource extends Resource
{
    protected static ?string $model = RelogradeOrder::class;

    protected static ?string $modelLabel = 'سفارش رلوگرید';

    protected static ?string $pluralModelLabel = 'سفارش‌های رلوگرید';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'سفارش‌ها';

    protected static string|\UnitEnum|null $navigationGroup = 'رلوگرید';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('اقلام')
                        ->schema([
                            Repeater::make('items')
                                ->label('اقلام')
                                ->minItems(1)
                                ->schema([
                                    Select::make('product_slug')
                                        ->label('محصول')
                                        ->searchable()
                                        ->required()
                                        ->getSearchResultsUsing(function (string $search) {
                                            return RelogradeProduct::query()
                                                ->where('name', 'like', "%{$search}%")
                                                ->orWhere('slug', 'like', "%{$search}%")
                                                ->limit(50)
                                                ->pluck('name', 'slug')
                                                ->toArray();
                                        })
                                        ->getOptionLabelUsing(function ($value): string {
                                            return RelogradeProduct::query()->where('slug', $value)->value('name') ?? (string) $value;
                                        })
                                        ->reactive(),
                                    TextInput::make('amount')
                                        ->label('تعداد')
                                        ->numeric()
                                        ->minValue(1)
                                        ->required(),
                                    TextInput::make('face_value')
                                        ->numeric()
                                        ->label('ارزش اسمی')
                                        ->visible(fn (Get $get) => self::productIsVariable($get('product_slug')))
                                        ->helperText(fn (Get $get) => self::productVariableRange($get('product_slug'))),
                                ])
                                ->columns(3),
                            TextInput::make('reference')
                                ->label('مرجع')
                                ->maxLength(250)
                                ->helperText('مرجع اختیاری برای تطبیق حسابداری.')
                                ->columnSpanFull(),
                        ]),
                    Step::make('پرداخت')
                        ->schema([
                            Select::make('connection_id')
                                ->label('اتصال')
                                ->options(fn () => RelogradeConnection::query()->pluck('name', 'id')->toArray())
                                ->default(fn () => RelogradeConnection::query()->default()->value('id'))
                                ->required()
                                ->live(),
                            Select::make('payment_currency')
                                ->label('ارز پرداخت')
                                ->options(function (Get $get) {
                                    $connectionId = $get('connection_id') ?? RelogradeConnection::query()->default()->value('id');
                                    if (! $connectionId) {
                                        return [];
                                    }

                                    return RelogradeAccount::query()
                                        ->where('connection_id', $connectionId)
                                        ->distinct()
                                        ->pluck('currency', 'currency')
                                        ->toArray();
                                })
                                ->helperText('برای نمایش ارزها ابتدا موجودی‌ها را همگام‌سازی کنید.')
                                ->required(),
                        ])
                        ->columns(2),
                    Step::make('بازبینی')
                        ->schema([
                            Placeholder::make('summary')
                                ->label('خلاصه')
                                ->content(fn (Get $get) => self::buildSummary($get)),
                        ]),
                    Step::make('تکمیل سفارش')
                        ->schema([
                            Select::make('fulfillment_policy')
                                ->label('روش تکمیل')
                                ->options([
                                    'confirm' => 'تایید (ممکن است در انتظار شود)',
                                    'resolve' => 'نهایی‌سازی (فقط تک‌قلم)',
                                    'none' => 'فقط ایجاد',
                                ])
                                ->default('confirm')
                                ->required(),
                        ]),
                ])
                    ->submitAction('ایجاد سفارش')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('connection.name')->label('اتصال')->toggleable(),
                TextColumn::make('trx')->label('شناسه تراکنش')->searchable(),
                TextColumn::make('reference')->label('مرجع')->toggleable(),
                TextColumn::make('state')
                    ->label('محیط')
                    ->badge()
                    ->formatStateUsing(fn ($state) => RelogradeLabels::environment($state)),
                TextColumn::make('order_status')
                    ->label('وضعیت سفارش')
                    ->badge()
                    ->formatStateUsing(fn ($state) => RelogradeLabels::orderStatus($state)),
                TextColumn::make('payment_currency')->label('ارز پرداخت')->badge()->toggleable(),
                TextColumn::make('price_amount')
                    ->label('قیمت')
                    ->formatStateUsing(fn ($state, RelogradeOrder $record) => RelogradeCurrencyFormatter::formatAmount($state, $record->price_currency, true))
                    ->toggleable(),
                ToggleColumn::make('downloaded')->label('دانلود شده'),
                TextColumn::make('date_created')->label('تاریخ ایجاد')->jalaliDateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('connection_id')
                    ->label('اتصال')
                    ->options(fn () => RelogradeConnection::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                SelectFilter::make('order_status')
                    ->label('وضعیت سفارش')
                    ->options(fn () => RelogradeOrder::query()
                        ->whereNotNull('order_status')
                        ->distinct()
                        ->pluck('order_status')
                        ->mapWithKeys(fn ($value) => [$value => RelogradeLabels::orderStatus($value)])
                        ->toArray())
                    ->searchable(),
                SelectFilter::make('state')
                    ->label('محیط')
                    ->options(fn () => RelogradeOrder::query()
                        ->whereNotNull('state')
                        ->distinct()
                        ->pluck('state')
                        ->mapWithKeys(fn ($value) => [$value => RelogradeLabels::environment($value)])
                        ->toArray())
                    ->searchable(),
            ])
            ->actions([
                ViewAction::make()->label('مشاهده'),
                Action::make('confirm')
                    ->label('تایید')
                    ->icon('heroicon-o-check')
                    ->visible(fn (RelogradeOrder $record) => $record->order_status === 'created' && RelogradeAuthorization::can('orders_fulfill'))
                    ->requiresConfirmation()
                    ->modalHeading('تایید سفارش')
                    ->modalSubmitActionLabel('تایید')
                    ->modalCancelActionLabel('انصراف')
                    ->action(function (RelogradeOrder $record, RelogradeOrderService $service) {
                        $service->confirmOrder($record);
                        RelogradeNotifier::success('سفارش تایید شد.');
                    }),
                Action::make('resolve')
                    ->label('نهایی‌سازی')
                    ->icon('heroicon-o-sparkles')
                    ->visible(fn (RelogradeOrder $record) => $record->order_status === 'created' && $record->items()->count() === 1 && RelogradeAuthorization::can('orders_fulfill'))
                    ->requiresConfirmation()
                    ->modalHeading('نهایی‌سازی سفارش')
                    ->modalSubmitActionLabel('نهایی‌سازی')
                    ->modalCancelActionLabel('انصراف')
                    ->action(function (RelogradeOrder $record, RelogradeOrderService $service) {
                        $service->resolveOrder($record);
                        RelogradeNotifier::success('سفارش نهایی شد.');
                    }),
                Action::make('cancel')
                    ->label('لغو')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (RelogradeOrder $record) => in_array($record->order_status, ['created', 'pending'], true) && RelogradeAuthorization::can('orders_fulfill'))
                    ->requiresConfirmation()
                    ->modalHeading('لغو سفارش')
                    ->modalSubmitActionLabel('لغو')
                    ->modalCancelActionLabel('انصراف')
                    ->action(function (RelogradeOrder $record, RelogradeOrderService $service) {
                        $service->cancelOrder($record);
                        RelogradeNotifier::success('سفارش لغو شد.');
                    }),
                Action::make('refresh')
                    ->label('به‌روزرسانی')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (RelogradeOrder $record, RelogradeOrderService $service) {
                        $service->refreshOrder($record);
                        RelogradeNotifier::success('سفارش به‌روزرسانی شد.');
                    }),
            ])
            ->defaultSort('date_created', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRelogradeOrders::route('/'),
            'create' => CreateRelogradeOrder::route('/create'),
            'view' => ViewRelogradeOrder::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('سفارش')
                    ->schema([
                        TextEntry::make('trx')->label('شناسه تراکنش'),
                        TextEntry::make('reference')->label('مرجع'),
                        TextEntry::make('state')
                            ->label('محیط')
                            ->badge()
                            ->formatStateUsing(fn ($state) => RelogradeLabels::environment($state)),
                        TextEntry::make('order_status')
                            ->label('وضعیت سفارش')
                            ->badge()
                            ->formatStateUsing(fn ($state) => RelogradeLabels::orderStatus($state)),
                        TextEntry::make('payment_status')
                            ->label('وضعیت پرداخت')
                            ->badge()
                            ->formatStateUsing(fn ($state) => RelogradeLabels::paymentStatus($state)),
                        TextEntry::make('payment_currency')->label('ارز پرداخت'),
                        TextEntry::make('price_amount')
                            ->label('قیمت')
                            ->formatStateUsing(fn ($state, RelogradeOrder $record) => RelogradeCurrencyFormatter::formatAmount($state, $record->price_currency, false)),
                        TextEntry::make('price_currency')->label('ارز قیمت'),
                        TextEntry::make('date_created')->label('تاریخ ایجاد')->jalaliDateTime(),
                        TextEntry::make('downloaded')
                            ->label('دانلود شده')
                            ->formatStateUsing(fn ($state) => RelogradeLabels::boolean((bool) $state)),
                    ])
                    ->columns(3),
                Section::make('اقلام')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('اقلام')
                            ->schema([
                                TextEntry::make('product_name')->label('محصول'),
                                TextEntry::make('product_slug')->label('اسلاگ'),
                                TextEntry::make('amount')->label('تعداد'),
                                TextEntry::make('face_value_amount')
                                    ->label('ارزش اسمی')
                                    ->formatStateUsing(fn ($state, $record) => RelogradeCurrencyFormatter::formatAmount($state, $record->face_value_currency ?? null, false)),
                                TextEntry::make('face_value_currency')->label('ارز'),
                                TextEntry::make('total_price_amount')
                                    ->label('جمع')
                                    ->formatStateUsing(fn ($state, $record) => RelogradeCurrencyFormatter::formatAmount($state, $record->payment_currency ?? null, false)),
                                RepeatableEntry::make('lines')
                                    ->label('ردیف‌ها')
                                    ->schema([
                                        TextEntry::make('tag')->label('برچسب'),
                                        TextEntry::make('status')
                                            ->label('وضعیت')
                                            ->formatStateUsing(fn ($state) => RelogradeLabels::orderStatus($state)),
                                        TextEntry::make('voucher_code')
                                            ->label('کد ووچر')
                                            ->formatStateUsing(function ($state) {
                                                if (RelogradeAuthorization::can('vouchers_reveal')) {
                                                    return $state;
                                                }

                                                return self::maskVoucher($state);
                                            }),
                                        TextEntry::make('voucher_serial')->label('سریال ووچر'),
                                        TextEntry::make('voucher_date_expired')->label('تاریخ انقضا')->jalaliDateTime(),
                                        TextEntry::make('voucher_url')->label('نشانی ووچر'),
                                    ])
                                    ->columns(3),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    protected static function productIsVariable(?string $slug): bool
    {
        if (! $slug) {
            return false;
        }

        return (bool) RelogradeProduct::query()->where('slug', $slug)->value('is_variable_product');
    }

    protected static function productVariableRange(?string $slug): string
    {
        if (! $slug) {
            return '';
        }

        $product = RelogradeProduct::query()->where('slug', $slug)->first();
        if (! $product || ! $product->is_variable_product) {
            return '';
        }

        $min = $product->face_value_min;
        $max = $product->face_value_max;
        $currency = $product->face_value_currency;

        return trim("بازه: {$min} تا {$max} {$currency}");
    }

    protected static function buildSummary(Get $get): string
    {
        $items = $get('items') ?? [];
        if (! is_array($items) || count($items) === 0) {
            return 'هیچ قلمی انتخاب نشده است.';
        }

        $totalAmount = 0;
        $lines = [];

        foreach ($items as $item) {
            $amount = (int) ($item['amount'] ?? 0);
            $totalAmount += $amount;
            $productSlug = $item['product_slug'] ?? '-';
            $lines[] = $productSlug.' × '.$amount;
        }

        return implode(' | ', $lines).' (جمع تعداد: '.$totalAmount.')';
    }

    protected static function maskVoucher(?string $value): string
    {
        if (! $value) {
            return '';
        }

        $length = strlen($value);
        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        return str_repeat('*', $length - 4).substr($value, -4);
    }
}
