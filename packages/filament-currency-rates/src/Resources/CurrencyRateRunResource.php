<?php

namespace Haida\FilamentCurrencyRates\Resources;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCurrencyRates\Models\CurrencyRateRun;
use Haida\FilamentCurrencyRates\Resources\CurrencyRateRunResource\Pages\ListCurrencyRateRuns;
use Haida\FilamentCurrencyRates\Resources\CurrencyRateRunResource\Pages\ViewCurrencyRateRun;
use Haida\FilamentCurrencyRates\Support\CurrencyRateLabels;

class CurrencyRateRunResource extends Resource
{
    protected static ?string $model = CurrencyRateRun::class;

    protected static ?string $modelLabel = 'گزارش همگام‌سازی';

    protected static ?string $pluralModelLabel = 'گزارش‌های همگام‌سازی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?string $navigationLabel = 'گزارش‌ها';

    protected static string|\UnitEnum|null $navigationGroup = 'نرخ ارز';

    protected static ?int $navigationSort = 1;

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
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => CurrencyRateLabels::statusLabel($state)),
                TextColumn::make('source')
                    ->label('منبع')
                    ->badge()
                    ->formatStateUsing(fn ($state) => CurrencyRateLabels::sourceLabel($state)),
                TextColumn::make('rates_count')
                    ->label('تعداد نرخ')
                    ->numeric(),
                TextColumn::make('duration_ms')
                    ->label('مدت (میلی‌ثانیه)')
                    ->numeric(),
                TextColumn::make('fetched_at')
                    ->label('زمان دریافت')
                    ->jalaliDateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('ثبت شده در')
                    ->jalaliDateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label('مشاهده')
                    ->url(fn (CurrencyRateRun $record) => self::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCurrencyRateRuns::route('/'),
            'view' => ViewCurrencyRateRun::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('جزئیات اجرا')
                    ->schema([
                        TextEntry::make('status')
                            ->label('وضعیت')
                            ->formatStateUsing(fn ($state) => CurrencyRateLabels::statusLabel($state)),
                        TextEntry::make('source')
                            ->label('منبع')
                            ->formatStateUsing(fn ($state) => CurrencyRateLabels::sourceLabel($state)),
                        TextEntry::make('rates_count')->label('تعداد نرخ'),
                        TextEntry::make('duration_ms')->label('مدت (میلی‌ثانیه)'),
                        TextEntry::make('fetched_at')->label('زمان دریافت')->jalaliDateTime(),
                        TextEntry::make('error_message')->label('پیام خطا')->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('جزئیات')
                    ->schema([
                        TextEntry::make('payload')
                            ->label('داده')
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
