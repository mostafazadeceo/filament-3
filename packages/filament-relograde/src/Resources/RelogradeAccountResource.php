<?php

namespace Haida\FilamentRelograde\Resources;

use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\FilamentRelograde\Jobs\SyncAccountsJob;
use Haida\FilamentRelograde\Models\RelogradeAccount;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Resources\RelogradeAccountResource\Pages\ListRelogradeAccounts;
use Haida\FilamentRelograde\Support\RelogradeAuthorization;
use Haida\FilamentRelograde\Support\RelogradeLabels;
use Haida\FilamentRelograde\Support\RelogradeNotifier;

class RelogradeAccountResource extends Resource
{
    protected static ?string $model = RelogradeAccount::class;

    protected static ?string $modelLabel = 'موجودی رلوگرید';

    protected static ?string $pluralModelLabel = 'موجودی‌های رلوگرید';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationLabel = 'موجودی‌ها';

    protected static string|\UnitEnum|null $navigationGroup = 'رلوگرید';

    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('connection.name')->label('اتصال')->toggleable(),
                TextColumn::make('currency')->label('ارز')->badge()->searchable(),
                TextColumn::make('state')
                    ->label('محیط')
                    ->badge()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => RelogradeLabels::environment($state)),
                TextColumn::make('total_amount')->label('مبلغ کل')->numeric(4),
                TextColumn::make('synced_at')->label('آخرین همگام‌سازی')->jalaliDateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('connection_id')
                    ->label('اتصال')
                    ->options(fn () => RelogradeConnection::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                SelectFilter::make('currency')
                    ->label('ارز')
                    ->options(fn () => RelogradeAccount::query()->distinct()->pluck('currency', 'currency')->toArray())
                    ->searchable(),
                SelectFilter::make('state')
                    ->label('محیط')
                    ->options(fn () => RelogradeAccount::query()
                        ->whereNotNull('state')
                        ->distinct()
                        ->pluck('state')
                        ->mapWithKeys(fn ($value) => [$value => RelogradeLabels::environment($value)])
                        ->toArray())
                    ->searchable(),
            ])
            ->headerActions([
                Action::make('sync')
                    ->label('همگام‌سازی موجودی‌ها')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn () => RelogradeAuthorization::can('sync'))
                    ->action(function () {
                        $connection = RelogradeConnection::query()->default()->first();
                        if (! $connection) {
                            RelogradeNotifier::error(new \RuntimeException('اتصال پیش‌فرض یافت نشد.'), 'اتصال پیش‌فرض یافت نشد.');

                            return;
                        }

                        SyncAccountsJob::dispatch($connection->getKey());
                        RelogradeNotifier::success('همگام‌سازی موجودی‌ها در صف قرار گرفت.');
                    }),
            ])
            ->defaultSort('synced_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRelogradeAccounts::route('/'),
        ];
    }
}
