<?php

namespace Haida\FilamentCurrencyRates\Resources;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCurrencyRates\Jobs\SyncCurrencyRatesJob;
use Haida\FilamentCurrencyRates\Models\CurrencyRate;
use Haida\FilamentCurrencyRates\Resources\CurrencyRateResource\Pages\ListCurrencyRates;
use Haida\FilamentCurrencyRates\Resources\CurrencyRateResource\Pages\ViewCurrencyRate;
use Haida\FilamentCurrencyRates\Services\CurrencyRateManager;
use Haida\FilamentCurrencyRates\Support\CurrencyRateLabels;
use Haida\FilamentCurrencyRates\Support\CurrencyUnit;

class CurrencyRateResource extends Resource
{
    protected static ?string $model = CurrencyRate::class;

    protected static ?string $modelLabel = 'نرخ ارز';

    protected static ?string $pluralModelLabel = 'نرخ‌های ارز';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'نرخ ارزها';

    protected static string|\UnitEnum|null $navigationGroup = 'نرخ ارز';

    protected static ?int $navigationSort = 0;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('کد')
                    ->formatStateUsing(fn ($state) => strtoupper((string) $state))
                    ->searchable(),
                TextColumn::make('name')
                    ->label('نام ارز')
                    ->searchable(),
                TextColumn::make('buy_price')
                    ->label('قیمت خرید')
                    ->formatStateUsing(function ($state, CurrencyRate $record) {
                        $manager = app(CurrencyRateManager::class);
                        $unit = $manager->displayUnit();
                        $value = $manager->getBuyPrice($record->code, $unit);
                        if ($value === null) {
                            return '-';
                        }

                        return number_format($value, 0).' '.CurrencyUnit::label($unit);
                    })
                    ->sortable(),
                TextColumn::make('sell_price')
                    ->label('قیمت فروش')
                    ->formatStateUsing(function ($state, CurrencyRate $record) {
                        $manager = app(CurrencyRateManager::class);
                        $unit = $manager->displayUnit();
                        $value = $manager->getSellPrice($record->code, $unit);
                        if ($value === null) {
                            return '-';
                        }

                        return number_format($value, 0).' '.CurrencyUnit::label($unit);
                    })
                    ->sortable(),
                TextColumn::make('source')
                    ->label('منبع')
                    ->badge()
                    ->formatStateUsing(fn ($state) => CurrencyRateLabels::sourceLabel($state)),
                TextColumn::make('fetched_at')
                    ->label('آخرین دریافت')
                    ->jalaliDateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label('مشاهده')
                    ->url(fn (CurrencyRate $record) => self::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ])
            ->headerActions([
                Action::make('sync')
                    ->label('همگام‌سازی نرخ‌ها')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function () {
                        SyncCurrencyRatesJob::dispatch();
                        Notification::make()
                            ->title('همگام‌سازی در صف قرار گرفت.')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('fetched_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCurrencyRates::route('/'),
            'view' => ViewCurrencyRate::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('جزئیات نرخ')
                    ->schema([
                        TextEntry::make('code')
                            ->label('کد')
                            ->formatStateUsing(fn ($state) => strtoupper((string) $state)),
                        TextEntry::make('name')->label('نام ارز'),
                        TextEntry::make('buy_price')
                            ->label('قیمت خرید')
                            ->formatStateUsing(function ($state, CurrencyRate $record) {
                                $manager = app(CurrencyRateManager::class);
                                $unit = $manager->displayUnit();
                                $value = $manager->getBuyPrice($record->code, $unit);
                                if ($value === null) {
                                    return '-';
                                }

                                return number_format($value, 0).' '.CurrencyUnit::label($unit);
                            }),
                        TextEntry::make('sell_price')
                            ->label('قیمت فروش')
                            ->formatStateUsing(function ($state, CurrencyRate $record) {
                                $manager = app(CurrencyRateManager::class);
                                $unit = $manager->displayUnit();
                                $value = $manager->getSellPrice($record->code, $unit);
                                if ($value === null) {
                                    return '-';
                                }

                                return number_format($value, 0).' '.CurrencyUnit::label($unit);
                            }),
                        TextEntry::make('source')
                            ->label('منبع')
                            ->formatStateUsing(fn ($state) => CurrencyRateLabels::sourceLabel($state)),
                        TextEntry::make('fetched_at')->label('آخرین دریافت')->jalaliDateTime(),
                        TextEntry::make('updated_at')->label('آخرین ویرایش')->jalaliDateTime(),
                    ])
                    ->columns(2),
                Section::make('داده خام')
                    ->schema([
                        TextEntry::make('raw_payload')
                            ->label('پاسخ')
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
