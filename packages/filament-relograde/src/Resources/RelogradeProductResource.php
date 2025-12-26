<?php

namespace Haida\FilamentRelograde\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Haida\FilamentRelograde\Clients\RelogradeClientFactory;
use Haida\FilamentRelograde\Jobs\SyncProductsJob;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Models\RelogradeProduct;
use Haida\FilamentRelograde\Resources\RelogradeProductResource\Pages\ListRelogradeProducts;
use Haida\FilamentRelograde\Resources\RelogradeProductResource\Pages\ViewRelogradeProduct;
use Haida\FilamentRelograde\Support\RelogradeAuthorization;
use Haida\FilamentRelograde\Support\RelogradeCurrencyFormatter;
use Haida\FilamentRelograde\Support\RelogradeLabels;
use Haida\FilamentRelograde\Support\RelogradeNotifier;
use Illuminate\Support\Collection;

class RelogradeProductResource extends Resource
{
    protected static ?string $model = RelogradeProduct::class;

    protected static ?string $modelLabel = 'محصول رلوگرید';

    protected static ?string $pluralModelLabel = 'محصولات رلوگرید';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'محصولات';

    protected static string|\UnitEnum|null $navigationGroup = 'رلوگرید';

    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('connection.name')->label('اتصال')->toggleable(),
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('slug')->label('اسلاگ')->toggleable(),
                TextColumn::make('brand_name')->label('برند')->toggleable(),
                TextColumn::make('face_value_currency')->label('ارز')->badge()->toggleable(),
                TextColumn::make('face_value_amount')
                    ->label('ارزش اسمی')
                    ->formatStateUsing(fn ($state, RelogradeProduct $record) => RelogradeCurrencyFormatter::formatAmount($state, $record->face_value_currency, false))
                    ->toggleable(),
                TextColumn::make('face_value_min')
                    ->label('حداقل')
                    ->formatStateUsing(fn ($state, RelogradeProduct $record) => RelogradeCurrencyFormatter::formatAmount($state, $record->face_value_currency, false))
                    ->toggleable(),
                TextColumn::make('face_value_max')
                    ->label('حداکثر')
                    ->formatStateUsing(fn ($state, RelogradeProduct $record) => RelogradeCurrencyFormatter::formatAmount($state, $record->face_value_currency, false))
                    ->toggleable(),
                TextColumn::make('price_amount')
                    ->label('قیمت')
                    ->formatStateUsing(fn ($state, RelogradeProduct $record) => RelogradeCurrencyFormatter::formatAmount($state, $record->price_currency, false))
                    ->toggleable(),
                IconColumn::make('is_variable_product')->label('متغیر')->boolean(),
                IconColumn::make('is_stocked')->label('موجود')->boolean(),
                TextColumn::make('synced_at')->label('آخرین همگام‌سازی')->jalaliDateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('connection_id')
                    ->label('اتصال')
                    ->options(fn () => RelogradeConnection::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                SelectFilter::make('brand_slug')
                    ->label('برند')
                    ->options(fn () => RelogradeProduct::query()->whereNotNull('brand_slug')->distinct()->pluck('brand_slug', 'brand_slug')->toArray())
                    ->searchable(),
                SelectFilter::make('category')
                    ->label('دسته‌بندی')
                    ->options(fn () => RelogradeProduct::query()->whereNotNull('category')->distinct()->pluck('category', 'category')->toArray())
                    ->searchable(),
                SelectFilter::make('redeem_type')
                    ->label('نوع بازخرید')
                    ->options(fn () => RelogradeProduct::query()->whereNotNull('redeem_type')->distinct()->pluck('redeem_type', 'redeem_type')->toArray())
                    ->searchable(),
                SelectFilter::make('redeem_value')
                    ->label('مقدار بازخرید')
                    ->options(fn () => RelogradeProduct::query()->whereNotNull('redeem_value')->distinct()->pluck('redeem_value', 'redeem_value')->toArray())
                    ->searchable(),
                SelectFilter::make('face_value_currency')
                    ->label('ارز')
                    ->options(fn () => RelogradeProduct::query()->whereNotNull('face_value_currency')->distinct()->pluck('face_value_currency', 'face_value_currency')->toArray())
                    ->searchable(),
                TernaryFilter::make('is_variable_product')
                    ->label('محصول متغیر')
                    ->trueLabel('بله')
                    ->falseLabel('خیر')
                    ->placeholder('همه'),
                TernaryFilter::make('is_stocked')
                    ->label('موجود')
                    ->trueLabel('بله')
                    ->falseLabel('خیر')
                    ->placeholder('همه'),
            ])
            ->actions([
                ViewAction::make()->label('مشاهده'),
                Action::make('refresh')
                    ->label('به‌روزرسانی')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (RelogradeProduct $record, RelogradeClientFactory $factory) {
                        $connection = RelogradeConnection::find($record->connection_id);
                        if (! $connection) {
                            RelogradeNotifier::error(new \RuntimeException('اتصال پیدا نشد.'), 'اتصال پیدا نشد.');

                            return;
                        }

                        $client = $factory->make($connection);
                        $payload = $client->listProducts(['slug' => $record->slug, '__nocache' => true]);
                        $productData = $payload['data'][0] ?? null;
                        if (! $productData) {
                            RelogradeNotifier::error(new \RuntimeException('محصول پیدا نشد.'), 'محصول پیدا نشد.');

                            return;
                        }

                        $record->update([
                            'name' => data_get($productData, 'name'),
                            'brand_slug' => data_get($productData, 'brandSlug'),
                            'brand_name' => data_get($productData, 'brandName'),
                            'category' => data_get($productData, 'category'),
                            'redeem_type' => data_get($productData, 'redeemType'),
                            'redeem_value' => data_get($productData, 'redeemValue'),
                            'is_stocked' => (bool) data_get($productData, 'isStocked', false),
                            'is_variable_product' => (bool) data_get($productData, 'isVariableProduct', false),
                            'face_value_currency' => data_get($productData, 'faceValueCurrency'),
                            'face_value_amount' => data_get($productData, 'faceValueAmount'),
                            'face_value_min' => data_get($productData, 'faceValueMin'),
                            'face_value_max' => data_get($productData, 'faceValueMax'),
                            'price_amount' => data_get($productData, 'priceAmount'),
                            'price_currency' => data_get($productData, 'priceCurrency'),
                            'fee_variable' => data_get($productData, 'feeVariable'),
                            'fee_fixed' => data_get($productData, 'feeFixed'),
                            'fee_currency' => data_get($productData, 'feeCurrency'),
                            'raw_json' => $productData,
                            'synced_at' => now(),
                        ]);

                        RelogradeNotifier::success('محصول به‌روزرسانی شد.');
                    }),
            ])
            ->bulkActions([
                BulkAction::make('export')
                    ->label('خروجی سی‌اس‌وی')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        $headers = [
                            'اسلاگ', 'نام', 'برند', 'دسته‌بندی', 'نوع بازخرید', 'مقدار بازخرید',
                            'محصول متغیر', 'موجود', 'ارز', 'ارزش اسمی',
                            'حداقل', 'حداکثر', 'قیمت', 'ارز قیمت',
                        ];

                        $callback = function () use ($records, $headers) {
                            $handle = fopen('php://output', 'w');
                            fputcsv($handle, $headers);
                            foreach ($records as $record) {
                                fputcsv($handle, [
                                    $record->slug,
                                    $record->name,
                                    $record->brand_name,
                                    $record->category,
                                    $record->redeem_type,
                                    $record->redeem_value,
                                    $record->is_variable_product ? '1' : '0',
                                    $record->is_stocked ? '1' : '0',
                                    $record->face_value_currency,
                                    $record->face_value_amount,
                                    $record->face_value_min,
                                    $record->face_value_max,
                                    $record->price_amount,
                                    $record->price_currency,
                                ]);
                            }
                            fclose($handle);
                        };

                        return response()->streamDownload($callback, 'محصولات-رلوگرید.csv');
                    })
                    ->modalHeading('خروجی محصولات')
                    ->modalSubmitActionLabel('خروجی')
                    ->modalCancelActionLabel('انصراف')
                    ->requiresConfirmation(),
            ])
            ->headerActions([
                Action::make('sync')
                    ->label('همگام‌سازی محصولات')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn () => RelogradeAuthorization::can('sync'))
                    ->action(function () {
                        $connection = RelogradeConnection::query()->default()->first();
                        if (! $connection) {
                            RelogradeNotifier::error(new \RuntimeException('اتصال پیش‌فرض یافت نشد.'), 'اتصال پیش‌فرض یافت نشد.');

                            return;
                        }

                        SyncProductsJob::dispatch($connection->getKey(), true);
                        RelogradeNotifier::success('همگام‌سازی محصولات در صف قرار گرفت.');
                    }),
            ])
            ->defaultSort('synced_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRelogradeProducts::route('/'),
            'view' => ViewRelogradeProduct::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('محصول')
                    ->schema([
                        TextEntry::make('name')->label('نام'),
                        TextEntry::make('slug')->label('اسلاگ'),
                        TextEntry::make('brand_name')->label('برند'),
                        TextEntry::make('category')->label('دسته‌بندی'),
                        TextEntry::make('redeem_type')->label('نوع بازخرید'),
                        TextEntry::make('redeem_value')->label('مقدار بازخرید'),
                        TextEntry::make('is_variable_product')
                            ->label('متغیر')
                            ->formatStateUsing(fn ($state) => RelogradeLabels::boolean((bool) $state)),
                        TextEntry::make('is_stocked')
                            ->label('موجود')
                            ->formatStateUsing(fn ($state) => RelogradeLabels::boolean((bool) $state)),
                        TextEntry::make('face_value_currency')->label('ارز'),
                        TextEntry::make('face_value_amount')
                            ->label('ارزش اسمی')
                            ->formatStateUsing(fn ($state, RelogradeProduct $record) => RelogradeCurrencyFormatter::formatAmount($state, $record->face_value_currency, false)),
                        TextEntry::make('face_value_min')
                            ->label('حداقل')
                            ->formatStateUsing(fn ($state, RelogradeProduct $record) => RelogradeCurrencyFormatter::formatAmount($state, $record->face_value_currency, false)),
                        TextEntry::make('face_value_max')
                            ->label('حداکثر')
                            ->formatStateUsing(fn ($state, RelogradeProduct $record) => RelogradeCurrencyFormatter::formatAmount($state, $record->face_value_currency, false)),
                        TextEntry::make('price_amount')
                            ->label('قیمت')
                            ->formatStateUsing(fn ($state, RelogradeProduct $record) => RelogradeCurrencyFormatter::formatAmount($state, $record->price_currency, false)),
                        TextEntry::make('price_currency')->label('ارز قیمت'),
                        TextEntry::make('fee_variable')->label('کارمزد متغیر'),
                        TextEntry::make('fee_fixed')->label('کارمزد ثابت'),
                        TextEntry::make('fee_currency')->label('ارز کارمزد'),
                        TextEntry::make('synced_at')->label('آخرین همگام‌سازی')->jalaliDateTime(),
                    ])
                    ->columns(3),
            ]);
    }
}
